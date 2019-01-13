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
            $result = 0;
            $result = max($this->call('packagist:root'), $result);
            $result = max($this->call('packagist:get'), $result);
            $result = max($this->call('packagist:info'), $result);
            $result = max($this->call('packagist:index'), $result);
            $result = max($this->call('packagist:sync'), $result);
            //            $result = max($this->call('packagist:purge'), $result);

            if ($result === 0) {
                $info = implode(' / ', [
                    cache('info_count'),
                    cache('info_size'),
                ]);
                $content = '🎉Reload completed! **' . $info . '**';
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
