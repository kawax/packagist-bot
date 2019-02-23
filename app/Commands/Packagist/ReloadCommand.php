<?php

namespace App\Commands\Packagist;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use App\Notifications\SimpleNotification;

use App\Jobs\NotifyJob;

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
    protected $description = 'main';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (cache()->lock('reload', 60 * 30)->get()) {
            $this->reload();

            cache()->lock('reload')->release();
        } else {
            NotifyJob::dispatchNow(new SimpleNotification('🔒Reload locked!'));

            return 1;
        }
    }

    protected function reload()
    {
        $result = rescue(function () {
            $this->call('packagist:root');
            $this->call('packagist:get');
            $this->call('packagist:info');
            $this->call('packagist:index');
            $this->call('packagist:sync');

            //            $this->call('packagist:purge');

            return true;
        }, false);

        if (!app()->environment('production')) {
            return;
        }

        if ($result) {
            $info = implode(' / ', [
                cache('info_count'),
                cache('info_size'),
            ]);
            $content = '🎉Reload completed! **' . $info . '**';
        } else {
            $content = '☠️Reload failed?';
        }

        NotifyJob::dispatchNow(new SimpleNotification($content));
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
        $schedule->command(static::class, ['--quiet'])
                 ->hourlyAt(50);
    }
}
