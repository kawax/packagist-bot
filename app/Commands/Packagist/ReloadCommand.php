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
        cache()->forever('info_size', '-');
        cache()->forever('info_count', '-');

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
        $result = rescue(
            function () {
                $this->call('packagist:root');
                $this->call('packagist:get');
                $this->call('packagist:info');
                $this->call('packagist:index');

                return cache('info_size') !== 'error';
            },
            false
        );

        if (! app()->isProduction()) {
            return;
        }

        if ($result) {
            $info = collect(
                [
                    cache('info_count'),
                    cache('info_size'),
                ]
            )->implode(' / ');

            $content = "Reload completed! **{$info}**";
        } else {
            $content = '☠️Reload failed?';
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
                 ->everyTenMinutes()
                 ->withoutOverlapping(60);
    }
}
