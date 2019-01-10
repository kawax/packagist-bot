<?php

namespace App\Commands\Packagist;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use Aws\CloudWatch\CloudWatchClient;
use Aws\Exception\AwsException;

class IndexCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'packagist:index';

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
        //        $this->cloudwatch();

        $last = now();

        view('welcome')->with(compact('last'))->publish('index.html');
    }

    /**
     * WIP
     */
    protected function cloudwatch()
    {
        $client = new CloudWatchClient([
            'credentials' => [
                'key'    => config('packagist.aws.key'),
                'secret' => config('packagist.aws.secret'),
            ],
            'region'      => config('packagist.aws.region'),
            'version'     => 'latest',
        ]);

        try {
            $result = $client->getMetricStatistics([
                'Namespace'  => 'AWS/S3',
                'MetricName' => 'NumberOfObjects',
                'Dimensions' => [
                    [
                        'Name'  => 'BucketName',
                        'Value' => config('packagist.s3.bucket'),
                    ],
                ],
                'StartTime'  => now()->subDays(2)->timestamp,
                'EndTime'    => now()->timestamp,
                'Period'     => 86400,
                'Statistics' => ['Maximum'],
            ]);

            dd($result);
        } catch (AwsException $e) {
            // output error message if fails
            $this->error($e->getMessage());
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
