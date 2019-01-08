<?php

namespace App\Commands\Packagist;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use Illuminate\Support\Facades\Notification;
use App\Notifications\ReloadNotification;

class ReloadCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'packagist:reload';

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
        if (cache()->has('reload.lock')) {
            Notification::route('discord', config('services.discord.channel'))
                        ->notify(new ReloadNotification('Reload locked!'));

            return;
        }

        cache(['reload.lock' => true], 60);

        $this->call('packagist:root');
        $this->call('packagist:get');
        $this->call('packagist:index');
        $result = $this->call('packagist:sync');
        //        $this->call('packagist:purge');

        cache()->forget('reload.lock');

        if ($result === 0) {
            $content = 'Reload completed!';
        } else {
            $content = 'Reload failed?';
        }

        Notification::route('discord', config('services.discord.channel'))
                    ->notify(new ReloadNotification($content));
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
        $schedule->command(static::class)->hourlyAt(50);
    }
}
