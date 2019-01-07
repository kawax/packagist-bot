<?php

namespace App\Commands\Packagist;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use Aws\CloudFront\CloudFrontClient;

class PurgeCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'packagist:purge';

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
        $paths = [
            '/',
            '/packages.json',
        ];

        $client = new CloudFrontClient([
            'credentials' => [
                'key'    => config('packagist.cloudfront.key'),
                'secret' => config('packagist.cloudfront.secret'),
            ],
            'region'      => 'us-east-1',
            'version'     => 'latest',
        ]);

        $result = $client->createInvalidation([
            'DistributionId'    => config('packagist.cloudfront.dist'),
            'InvalidationBatch' => [
                'Paths'           => [
                    'Quantity' => count($paths),
                    'Items'    => $paths,
                ],
                'CallerReference' => now()->timestamp,
            ],
        ]);
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
