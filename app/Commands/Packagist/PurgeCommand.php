<?php

namespace App\Commands\Packagist;

use App\Jobs\NotifyJob;
use App\Notifications\SimpleNotification;
use Aws\CloudFront\CloudFrontClient;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

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
    protected $description = 'Purge CloudFront Cache';

    /**
     * @var array
     */
    protected $paths;

    /**
     * PurgeCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->paths = [
            '/',
            '/'.config('packagist.root'),
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Throwable
     */
    public function handle()
    {
        if (blank(config('packagist.cloudfront.dist'))) {
            $this->error('Set CloudFront Distribution ID');

            return 1;
        }

        $lock = cache()->lock('purge', 60 * 2);

        if (! $lock->get()) {
            NotifyJob::dispatchNow(new SimpleNotification('🔒Purge rate limit!'));

            return 1;
        }

        $this->purge();
    }

    protected function purge()
    {
        $client = app(CloudFrontClient::class);

        $result = $client->createInvalidation(
            [
                'DistributionId'    => config('packagist.cloudfront.dist'),
                'InvalidationBatch' => [
                    'Paths'           => [
                        'Quantity' => count($this->paths),
                        'Items'    => $this->paths,
                    ],
                    'CallerReference' => now()->timestamp,
                ],
            ]
        );

        $status = data_get($result, 'Invalidation.Status', 'Error?');
        $content = "🧹Purge start... **{$status}**";

        NotifyJob::dispatchNow(new SimpleNotification($content));
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     *
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        $schedule->command(static::class)
                 ->dailyAt('18:50')
                 ->when(app()->isProduction());
    }
}
