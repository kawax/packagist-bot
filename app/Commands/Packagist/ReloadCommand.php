<?php

namespace App\Commands\Packagist;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use Illuminate\Support\Facades\Notification;
use App\Notifications\SimpleNotification;

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
        if (cache()->lock('reload', 60 * 30)->get()) {
            $this->call('packagist:root');
            $this->call('packagist:get');
            $this->call('packagist:index');
            $this->call('packagist:info');
            $result = $this->call('packagist:sync');
            //            $this->call('packagist:purge');

            if ($result === 0) {
                $content = '🎉Reload completed! **' . cache('info_size') . '**';
            } else {
                $content = '☠️Reload failed?';
            }

            Notification::route('discord', config('services.discord.channel'))
                        ->notify(new SimpleNotification($content));

            cache()->lock('reload')->release();
        } else {
            Notification::route('discord', config('services.discord.channel'))
                        ->notify(new SimpleNotification('🔒Reload locked!'));
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
        $schedule->command(static::class)->hourlyAt(50);
    }
}
