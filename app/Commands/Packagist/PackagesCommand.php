<?php

namespace App\Commands\Packagist;

use Illuminate\Console\Scheduling\Schedule;
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
    protected $signature = 'packagist:get {--P|provider=}';

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

    protected function providers()
    {
        $packages = json_decode(Storage::get($this->path . 'packages.json'));

        $providers = data_get($packages, 'provider-includes');

        $urls = [];

        foreach ($providers as $provider => $sha) {
            if (filled($this->option('provider')) and $this->option('provider') !== $provider) {
                continue;
            }

            $urls[] = [
                'provider' => $provider,
                'url'      => str_replace('%hash%', $sha->sha256, $provider),
            ];
        }

        $requests = function ($urls) {
            foreach ($urls as $url) {
                yield function () use ($url) {
                    return $this->client->get($url['url']);
                };
            }
        };

        $pool = new Pool($this->client, $requests($urls), [
            'concurrency' => config('packagist.concurrency'),
            'fulfilled'   => function ($res, $index) use ($urls) {
                $this->task('<info>Provider: </info>' . basename($urls[$index]['url']));

                if (!Storage::exists($this->path . $urls[$index]['url'])) {
                    Storage::put($this->path . $urls[$index]['url'], $res->getBody()->getContents());

                    $this->package($urls[$index]['url']);

                    $this->deleteProvider($urls[$index]);
                }
            },
            'rejected'    => function ($reason, $index) use ($urls) {
                $this->info('Provider Fail : ' . $urls[$index]['url']);
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();
    }

    protected function deleteProvider($url)
    {
        $dir = str_replace('%hash%.json', '*', Storage::path($this->path . $url['provider']));
        foreach (File::glob($dir) as $file) {
            if ($file !== Storage::path($this->path . $url['url'])) {
                File::delete($file);
                $this->line('Delete : ' . basename($file));
            }
        }
    }

    protected function package($provider)
    {
        $packages = json_decode(Storage::get($this->path . $provider))->providers;

        $urls = [];
        foreach ($packages as $package => $sha) {
            if (cache($package, '') === $sha->sha256) {
                continue;
            }

            $urls[] = [
                'package' => $package,
                'url'     => 'p/' . $package . '$' . $sha->sha256 . '.json',
            ];

            cache()->forever($package, $sha->sha256);

            if (count($urls) > 10) {
                break;
            }
        }

        $bar = $this->output->createProgressBar(count($urls));
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');

        $bar->start();

        $requests = function ($urls) {
            foreach ($urls as $url) {
                yield function () use ($url) {
                    return $this->client->getAsync($url['url']);
                };
            }
        };

        $pool = new Pool($this->client, $requests($urls), [
            'concurrency' => config('packagist.concurrency'),
            'fulfilled'   => function ($res, $index) use ($urls, $bar) {
                $package = $urls[$index]['package'];

                Storage::put($this->path . $urls[$index]['url'], $res->getBody()->getContents());

                $this->deletePackage($urls[$index]);

                $bar->advance();
                $bar->setMessage($package);
            },
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

    protected function deletePackage($url)
    {
        $dir = Storage::path($this->path . 'p/' . $url['package']) . '*';

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
