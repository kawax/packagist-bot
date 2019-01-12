<?php

namespace App\Commands\Packagist;

use LaravelZero\Framework\Commands\Command;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use Psr\Http\Message\ResponseInterface;

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
        $this->client = resolve(Client::class);

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
            $file = $urls[$index]['url'];

            $this->task('<info>Provider: </info>' . basename($file));

            $this->providerDelete($urls[$index]);

            $content = $res->getBody()->getContents();

            if (hash('sha256', $content) === $urls[$index]['sha']) {
                Storage::put($this->path . $file, $content);
                $this->package($file);
            } else {
                $this->error('Hash error: ' . $file);
            }
        };

        $pool = new Pool($this->client, $requests($urls), [
            'concurrency' => config('packagist.concurrency'),
            'fulfilled'   => $fulfilled,
            'rejected'    => function ($reason, $index) use ($urls) {
                $this->error('Provider Fail : ' . $urls[$index]['url']);
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
        $providers = json_decode(Storage::get($this->path . 'packages.json'));

        $providers = data_get($providers, 'provider-includes');

        return collect($providers)
            ->when(filled($this->argument('provider')), function (Collection $collect) {
                return $collect->only($this->argument('provider'));
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

            if (hash('sha256', $content) === $urls[$index]['sha']) {
                Storage::put($this->path . $urls[$index]['url'], $content);
            } else {
                $this->error('Hash error: ' . $package);
            }

            $this->packageDelete($urls[$index]);

            $bar->advance();
            $bar->setMessage($package);
        };

        $pool = new Pool($this->client, $requests($urls), [
            'concurrency' => config('packagist.concurrency'),
            'fulfilled'   => $fulfilled,
            'rejected'    => function ($reason, $index) use ($urls, $bar) {
                $this->error('Package Fail: ' . $urls[$index]['package']);
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
    protected function packageUrls(string $provider): Collection
    {
        $packages = json_decode(Storage::get($this->path . $provider));

        $packages = data_get($packages, 'providers');

        return collect($packages)
            ->reject(function ($meta, $package) {
                return Storage::exists($this->path . $this->packageFile($package, $meta));
            })->map(function ($meta, $package) {
                return [
                    'package' => $package,
                    'url'     => $this->packageFile($package, $meta),
                    'sha'     => data_get($meta, 'sha256'),
                ];
            })->values();
    }

    /**
     * @param string $package
     * @param object $meta
     *
     * @return string
     */
    protected function packageFile(string $package, $meta): string
    {
        return sprintf('p/%s$%s.json', $package, data_get($meta, 'sha256'));
    }

    /**
     * @param array $url
     */
    protected function packageDelete(array $url)
    {
        $pattern = Storage::path($this->path . 'p/' . $url['package']) . '$*';

        foreach (File::glob($pattern) as $file) {
            if ($file !== Storage::path($this->path . $url['url'])) {
                File::delete($file);
            }
        }
    }
}
