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

use Illuminate\Support\Facades\Notification;
use App\Notifications\HashErrorNotification;

class GetCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'packagist:get {provider?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Download provider files';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $path;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->client = resolve(Client::class);

        $this->path = config('packagist.path');

        if (!Storage::exists($this->path . config('packagist.root'))) {
            $this->call('packagist:root');
        }

        $this->providers();
    }

    /**
     *
     */
    protected function providers()
    {
        $urls = $this->providerUrls();

        $requests = function ($urls) {
            foreach ($urls as $url) {
                yield function () use ($url) {
                    $method = $this->option('quiet') ? 'getAsync' : 'get';

                    return $this->client->{$method}($url['url']);
                };
            }
        };

        $pool = new Pool($this->client, $requests($urls), [
            'concurrency' => config('packagist.concurrency'),
            'fulfilled'   => $this->providerFulfilled($urls),
            'rejected'    => function ($reason, $index) use ($urls) {
                $this->error('Provider rejected: ' . $urls[$index]['url']);
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();
    }

    /**
     * @return Collection
     */
    protected function providerUrls(): Collection
    {
        $providers = json_decode(Storage::get($this->path . config('packagist.root')));

        $providers = data_get($providers, 'provider-includes');

        return collect($providers)
            ->when(filled($this->argument('provider')), function (Collection $collection) {
                return $collection->only($this->argument('provider'));
            })->reject(function ($meta, $provider) {
                return Storage::exists($this->path . $this->providerFile($provider, $meta));
            })->map(function ($meta, $provider) {
                return [
                    'provider' => $provider,
                    'url'      => $this->providerFile($provider, $meta),
                    'sha'      => data_get($meta, 'sha256'),
                ];
            })->values();
    }

    /**
     * @param Collection $urls
     *
     * @return Closure
     */
    protected function providerFulfilled(Collection $urls): Closure
    {
        return function (ResponseInterface $res, $index) use ($urls) {
            $file = $urls[$index]['url'];

            $this->task('<info>Provider: </info>' . basename($file));

            $this->providerDelete($urls[$index]);

            $content = $res->getBody()->getContents();

            if (!hash_equals(hash('sha256', $content), $urls[$index]['sha'])) {
                Notification::route('discord', config('services.discord.channel'))
                            ->notify(new HashErrorNotification($urls[$index]['provider'], $file));

                return;
            }

            Storage::put($this->path . $file, $content);

            $this->call('packagist:package', [
                'provider' => $file,
            ]);
        };
    }

    /**
     * @param string $provider
     * @param object $meta
     *
     * @return string
     */
    protected function providerFile(string $provider, $meta): string
    {
        return str_replace('%hash%', data_get($meta, 'sha256'), $provider);
    }

    /**
     * @param array $url
     */
    protected function providerDelete(array $url)
    {
        $pattern = str_replace('%hash%.json', '*', Storage::path($this->path . $url['provider']));

        foreach (File::glob($pattern) as $file) {
            if ($file !== Storage::path($this->path . $url['url'])) {
                File::delete($file);
                $this->line('Delete: ' . basename($file));
            }
        }
    }
}
