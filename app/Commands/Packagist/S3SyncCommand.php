<?php

namespace App\Commands\Packagist;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use Illuminate\Support\Facades\Storage;

use Symfony\Component\Process\Process;

class S3SyncCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'packagist:sync';

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
        if (empty(config('packagist.s3.sync'))) {
            $this->error('S3_SYNC is empty');

            return;
        }

        Process::fromShellCommandline(config('packagist.s3.sync'))
               ->setWorkingDirectory(Storage::path(config('packagist.path')))
               ->setTimeout(config('packagist.s3.timeout'))
               ->run(function ($type, $buffer) {
                   if (Process::ERR === $type) {
                       $this->error($buffer);
                   } else {
                       $this->line($buffer);
                   }
               });
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
        // $schedule->command(static::class)->everyMinute();
    }
}
