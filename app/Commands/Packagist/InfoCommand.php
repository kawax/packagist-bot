<?php

namespace App\Commands\Packagist;

use App\Jobs\InfoCountJob;
use App\Jobs\InfoSizeJob;
use App\Jobs\NotifyJob;
use App\Notifications\SimpleNotification;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class InfoCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'packagist:info';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Update info';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        InfoCountJob::dispatch();
        InfoSizeJob::dispatch();
    }

    /**
     * Define the command's schedule.
     *
     * @param  Schedule  $schedule
     *
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        $schedule->command(static::class, ['--quiet'])
                 ->hourlyAt(5);
    }
}
