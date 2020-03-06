<?php

namespace App\Commands\Packagist;

use App\Jobs\NotifyJob;
use App\Notifications\SimpleNotification;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

/**
 * @codeCoverageIgnore
 */
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
     * @throws \Exception
     */
    public function handle()
    {
        $lock = cache()->lock('reload', 60 * 30);

        if ($lock->get()) {
            $this->reload();

            $lock->release();
        } else {
            NotifyJob::dispatchNow(new SimpleNotification('🔒Reload locked!'));

            return 1;
        }
    }

    /**
     * @throws \Exception
     */
    protected function reload()
    {
        cache()->forever('info_size', '-');
        cache()->forever('info_count', '-');

        $result = rescue(
            function () {
                $this->call('packagist:root');
                $this->call('packagist:get');
                if (! cache('suspend_info', false)) {
                    $this->call('packagist:info');
                }
                $this->call('packagist:index');
                $this->call('packagist:sync');

                //            $this->call('packagist:purge');

                return true;
            },
            false
        );

        if (! app()->isProduction()) {
            return;
        }

        if ($result) {
            $info = implode(
                ' / ',
                [
                    cache('info_count'),
                    cache('info_size'),
                ]
            );
            $content = 'Reload completed! **'.$info.'**';
        } else {
            $content = '☠️Reload failed?';
            cache(['suspend_info' => true], now()->addHours(12));
        }

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
        $schedule->command(static::class, ['--quiet'])
                 ->hourlyAt(30)
                 ->unlessBetween('16:00', '23:00');
    }
}
