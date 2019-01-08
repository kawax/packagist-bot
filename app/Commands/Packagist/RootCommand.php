<?php

namespace App\Commands\Packagist;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use Illuminate\Support\Facades\Storage;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

class RootCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'packagist:root';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new Client(config('packagist.guzzle'));

        $client->getAsync('packages.json')
               ->then(function (ResponseInterface $res) {
                   $json = $res->getBody()->getContents();
                   Storage::put(config('packagist.path') . 'packages.json', $json);

                   $this->task('packages.json');

                   $packages = json_decode($json);

                   $providers = data_get($packages, 'provider-includes');

                   foreach ($providers as $provider => $sha) {
                       $this->info($provider);
                   }
               }, function (RequestException $e) {
                   $this->error($e->getMessage());
               })->wait();
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
