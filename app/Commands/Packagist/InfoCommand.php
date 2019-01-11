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
        $process = new Process(
            explode(' ', 'du -sh'),
            Storage::path(config('packagist.path'))
        );

        $process->run();

        $size = $process->isSuccessful() ? $process->getOutput() : 'error';
        $size = rtrim($size, ". \n");

        $this->info($size);

        cache()->forever('info_size', $size);
    }

    /**
     * File Count
     */
    protected function fileCount()
    {
        $find = new Process(
            explode(' ', 'find . -type f'),
            Storage::path(config('packagist.path'))
        );
        $find->run();
        $file = $find->isSuccessful() ? $find->getOutput() : '';

        $process = new Process(explode(' ', 'wc -l'));
        $process->setInput($file);
        $process->run();

        $count = $process->isSuccessful() ? $process->getOutput() : 0;
        $count = trim($count);

        $count = number_format($count);

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
