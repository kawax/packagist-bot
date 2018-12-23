<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;

class PackagistGetCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'packagist:get';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->client = new Client(config('packagist.guzzle'));

        $this->packages();

        $this->providers();
    }

    /**
     * 1. packages.jsonをダウンロード
     */
    protected function packages()
    {
        $this->client->getAsync('/packages.json')
                     ->then(function (ResponseInterface $res) {
                         Storage::put('packages.json', $res->getBody()->getContents());
                         $this->task('packages.json');

                     }, function (RequestException $e) {
                         $this->error($e->getMessage());
                     })->wait();
    }

    protected function providers()
    {
        $packages = json_decode(Storage::get('packages.json'));

        $providers = data_get($packages, 'provider-includes');

        $urls = [];

        foreach ($providers as $provider => $sha) {
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
                if (!Storage::exists($urls[$index]['url'])) {
                    Storage::put($urls[$index]['url'], $res->getBody()->getContents());

                    $dir = str_replace('%hash%.json', '*', Storage::path($urls[$index]['provider']));
                    foreach (File::glob($dir) as $file) {
                        if ($file !== Storage::path($urls[$index]['url'])) {
                            File::delete($file);
                            $this->line('Delete : ' . basename($file));
                        }
                    }

                    $this->package($urls[$index]['url']);
                }

                $this->task('<info>Provider: </info>' . basename($urls[$index]['url']));
            },
            'rejected'    => function ($reason, $index) use ($urls) {
                $this->info('Provider Fail : ' . $urls[$index]['url']);
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();
    }

    protected function package($provider)
    {
        $packages = json_decode(Storage::get($provider))->providers;

        $urls = [];
        foreach ($packages as $package => $sha) {
            $urls[] = [
                'package' => $package,
                'url'     => '/p/' . $package . '$' . $sha->sha256 . '.json',
            ];

            if (count($urls) > 20) {
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

                if (!Storage::exists('/p/' . $package . '.json')) {
                    Storage::put('/p/' . $package . '.json', $res->getBody()->getContents());
                }

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
