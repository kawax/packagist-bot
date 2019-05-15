<?php

namespace App\Commands\Packagist;

use LaravelZero\Framework\Commands\Command;

use Closure;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use Psr\Http\Message\ResponseInterface;

use App\Notifications\HashErrorNotification;

use App\Jobs\NotifyJob;

use Symfony\Component\Console\Helper\ProgressBar;

class PackageCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'packagist:package {provider}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Download package files';

    /**
     * @var Client
     */
    protected $client;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->client = resolve(Client::class);

        $this->package($this->argument('provider'));
    }

    /**
     * @param  string  $provider
     */
    protected function package(string $provider)
    {
        $urls = $this->packageUrls($provider);

        $bar = $this->output->createProgressBar($urls->count());
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
        $bar->setMessage('');
        $bar->start();

        $requests = function ($urls) {
            foreach ($urls as $url) {
                yield function () use ($url) {
                    return $this->client->getAsync($url['url']);
                };
            }
        };

        $config = [
            'concurrency' => config('packagist.concurrency'),
            'fulfilled'   => $this->packageFulfilled($urls, $bar),
            'rejected'    => function ($reason, $index) use ($urls, $bar) {
                $this->error('Package rejected: '.$urls[$index]['package']);
                $bar->advance();
            },
        ];

        $pool = app(Pool::class, [
            'client'   => $this->client,
            'requests' => $requests($urls),
            'config'   => $config,
        ]);

        $pool->promise()->wait();

        $bar->finish();
        $this->line('');
    }

    /**
     * @param  string  $provider
     *
     * @return Collection
     */
    protected function packageUrls(string $provider): Collection
    {
        $packages = json_decode(Storage::get($provider));

        $packages = data_get($packages, 'providers');

        return collect($packages)
            ->unless(app()->environment('production'), function (Collection $collection) {
                return $collection->take(10);
            })->reject(function ($meta, $package) {
                return Storage::exists($this->packageFile($package, $meta));
            })->map(function ($meta, $package) {
                return [
                    'package' => $package,
                    'url'     => $this->packageFile($package, $meta),
                    'sha'     => data_get($meta, 'sha256'),
                ];
            })->values();
    }

    /**
     * @param  Collection  $urls
     * @param  ProgressBar  $bar
     *
     * @return Closure
     */
    protected function packageFulfilled(Collection $urls, ProgressBar $bar): Closure
    {
        return function (ResponseInterface $res, $index) use ($urls, $bar) {
            $package = $urls[$index]['package'];

            $content = $res->getBody()->getContents();

            if (hash_equals(hash('sha256', $content), $urls[$index]['sha'])) {
                Storage::put($urls[$index]['url'], $content);
            } else {
                $this->hashError($package, $urls[$index]['url']);
            }

            $this->packageDelete($urls[$index]);

            $bar->advance();
            $bar->setMessage($package);
        };
    }

    /**
     * @param  string  $package
     * @param  object  $meta
     *
     * @return string
     */
    protected function packageFile(string $package, $meta): string
    {
        return sprintf('p/%s$%s.json', $package, data_get($meta, 'sha256'));
    }

    /**
     * @param  array  $url
     */
    protected function packageDelete(array $url)
    {
        $pattern = Storage::path('p/'.$url['package']).'$*';

        foreach (File::glob($pattern) as $file) {
            if ($file !== Storage::path($url['url'])) {
                File::delete($file);
            }
        }
    }

    /**
     * @param  string  $title
     * @param  string  $url
     */
    protected function hashError(string $title, string $url)
    {
        $this->error('Hash error: '.$url);

        NotifyJob::dispatchNow(new HashErrorNotification($title, $url));
    }
}
