<?php

namespace App\Commands\Packagist;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;

class PackagesCommand extends Command
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
    protected $description = 'Command description';

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
        $this->client = new Client(config('packagist.guzzle'));

        $this->path = config('packagist.path');

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
                    return $this->client->get($url['url']);
                };
            }
        };

        $fulfilled = function (ResponseInterface $res, $index) use ($urls) {
            $this->task('<info>Provider: </info>' . basename($urls[$index]['url']));

            if (Storage::exists($this->path . $urls[$index]['url'])) {
                return;
            }

            $content = $res->getBody()->getContents();
            if ($urls[$index]['sha'] === hash('sha256', $content)) {
                Storage::put($this->path . $urls[$index]['url'], $content);

                $this->package($urls[$index]['url']);
            } else {
                $this->error('Hash error: ' . $urls[$index]['provider']);
            }

            $this->deleteProvider($urls[$index]);
        };

        $pool = new Pool($this->client, $requests($urls), [
            'concurrency' => config('packagist.concurrency'),
            'fulfilled'   => $fulfilled,
            'rejected'    => function ($reason, $index) use ($urls) {
                $this->info('Provider Fail : ' . $urls[$index]['url']);
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();
    }

    /**
     * @return Collection
     */
    protected function providerUrls()
    {
        $providers = json_decode(Storage::get($this->path . 'packages.json'));

        $providers = data_get($providers, 'provider-includes');

        return collect($providers)
            ->when(filled($this->argument('provider')), function (Collection $collect) {
                return $collect->filter(function ($meta, $provider) {
                    return $this->argument('provider') === $provider;
                });
            })->map(function ($meta, $provider) {
                return [
                    'provider' => $provider,
                    'url'      => str_replace('%hash%', data_get($meta, 'sha256'), $provider),
                    'sha'      => data_get($meta, 'sha256'),
                ];
            })->values();
    }

    /**
     * @param array $url
     */
    protected function deleteProvider(array $url)
    {
        $dir = str_replace('%hash%.json', '*', Storage::path($this->path . $url['provider']));
        foreach (File::glob($dir) as $file) {
            if ($file !== Storage::path($this->path . $url['url'])) {
                File::delete($file);
                $this->line('Delete : ' . basename($file));
            }
        }
    }

    /**
     * @param string $provider
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

        $fulfilled = function (ResponseInterface $res, $index) use ($urls, $bar) {
            $package = $urls[$index]['package'];

            $content = $res->getBody()->getContents();
            if ($urls[$index]['sha'] === hash('sha256', $content)) {
                Storage::put($this->path . $urls[$index]['url'], $content);
            } else {
                $this->error('Hash error: ' . $urls[$index]['package']);
            }

            $this->deletePackage($urls[$index]);

            $bar->advance();
            $bar->setMessage($package);
        };

        $pool = new Pool($this->client, $requests($urls), [
            'concurrency' => config('packagist.concurrency'),
            'fulfilled'   => $fulfilled,
            'rejected'    => function ($reason, $index) use ($urls, $bar) {
                $this->info('Package Fail: ' . $urls[$index]['package']);
                $bar->advance();
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();

        $bar->finish();
        $this->line('');
    }

    /**
     * @param string $provider
     *
     * @return Collection
     */
    protected function packageUrls(string $provider)
    {
        $packages = json_decode(Storage::get($this->path . $provider));

        $packages = data_get($packages, 'providers');

        return collect($packages)
            ->reject(function ($meta, $package) {
                return Storage::exists($this->path . $this->packageFile($package, data_get($meta, 'sha256')));
            })->map(function ($meta, $package) {
                return [
                    'package' => $package,
                    'url'     => $this->packageFile($package, data_get($meta, 'sha256')),
                    'sha'     => data_get($meta, 'sha256'),
                ];
            })->values();
    }

    /**
     * @param string $package
     * @param string $sha
     *
     * @return string
     */
    protected function packageFile(string $package, string $sha): string
    {
        return 'p/' . $package . '$' . $sha . '.json';
    }

    /**
     * @param array $url
     */
    protected function deletePackage(array $url)
    {
        $dir = Storage::path($this->path . 'p/' . $url['package']) . '$*';

        foreach (File::glob($dir) as $file) {
            if ($file !== Storage::path($this->path . $url['url'])) {
                File::delete($file);
            }
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
