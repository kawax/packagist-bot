<?php

namespace App\Commands\Packagist;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use Illuminate\Support\Facades\Storage;
use App\Notifications\HealthCheckNotification;

use App\Jobs\NotifyJob;

/**
 * @codeCoverageIgnore
 */
class HealthCheckCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'packagist:health';

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
        $before = (int) cache('root_modified', 0);

        $last = Storage::lastModified(config('packagist.root'));

        if ($before === $last) {
            NotifyJob::dispatchNow(new HealthCheckNotification($last));
        }

        cache()->forever('root_modified', $last);
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
        $schedule->command(static::class)->dailyAt('17:00');
    }
}
