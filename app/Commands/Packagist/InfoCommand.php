<?php

namespace App\Commands\Packagist;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use Illuminate\Support\Facades\Storage;

use Symfony\Component\Process\Process;

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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->fileSize();
        $this->fileCount();
    }

    /**
     * File Size
     */
    protected function fileSize()
    {
        $size = rescue(function () {
            $size = Process::fromShellCommandline('du -sh')
                           ->setWorkingDirectory(Storage::path(config('packagist.path')))
                           ->mustRun()
                           ->getOutput();

            return rtrim($size, ". \n");
        }, 'error');

        $this->info($size);

        cache()->forever('info_size', $size);
    }

    /**
     * File Count
     */
    protected function fileCount()
    {
        $count = count(Storage::allFiles(config('packagist.path')));

        $this->info($count);

        cache()->forever('info_count', $count);
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
