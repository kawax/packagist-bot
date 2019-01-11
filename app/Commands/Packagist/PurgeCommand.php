<?php

namespace App\Commands\Packagist;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use Aws\CloudFront\CloudFrontClient;

use Illuminate\Support\Facades\Notification;
use App\Notifications\SimpleNotification;

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

        if (empty(config('packagist.cloudfront.dist'))) {
            $this->error('Set CloudFront Distribution ID');

            return;
        }

        if (cache()->lock('purge', 60 * 2)->get() === false) {
            Notification::route('discord', config('services.discord.channel'))
                        ->notify(new SimpleNotification('🔒Purge rate limit!'));

            return;
        }

        $client = new CloudFrontClient([
            'credentials' => [
                'key'    => config('packagist.aws.key'),
                'secret' => config('packagist.aws.secret'),
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

        $status = data_get($result, 'Invalidation.Status', 'Error?');
        $content = "🧹Purge start... **{$status}**";

        Notification::route('discord', config('services.discord.channel'))
                    ->notify(new SimpleNotification($content));
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
        $schedule->command(static::class)->dailyAt('12:00');
    }
}
