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
        $du = new Process(explode(' ', 'du -sh'));
        $du->setWorkingDirectory(Storage::path(config('packagist.path')));

        $du->run();

        $size = $du->isSuccessful() ? $du->getOutput() : 'error';
        $size = rtrim($size, ". \n");

        $this->info($size);

        cache()->forever('info_size', $size);
    }

    /**
     * File Count
     */
    protected function fileCount()
    {
        $find = new Process(explode(' ', 'find . -type f'));
        $find->setWorkingDirectory(Storage::path(config('packagist.path')));

        $find->run();
        $file = $find->isSuccessful() ? $find->getOutput() : '';

        $wc = new Process(explode(' ', 'wc -l'));
        $wc->setInput($file);
        $wc->run();

        $count = $wc->isSuccessful() ? $wc->getOutput() : 0;
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
