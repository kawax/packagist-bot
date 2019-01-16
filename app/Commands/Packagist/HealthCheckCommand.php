<?php

namespace App\Commands\Packagist;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\HealthCheckNotification;

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
        $before = cache('root_modified', 0);

        $last = Storage::lastModified(config('packagist.path') . config('packagist.root'));

        if ($before == $last) {
            Notification::route('discord', config('services.discord.channel'))
                        ->notifyNow(new HealthCheckNotification($last));
        }

        cache()->forever('root_modified', $last);
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
        $schedule->command(static::class)->dailyAt('17:00');
    }
}
