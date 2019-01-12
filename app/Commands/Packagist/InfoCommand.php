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
        $size = with(new Process(explode(' ', 'du -sh')), function (Process $du) {
            $du->setWorkingDirectory(Storage::path(config('packagist.path')))
               ->run();

            $size = $du->isSuccessful() ? $du->getOutput() : 'error';

            return rtrim($size, ". \n");
        });

        $this->info($size);

        cache()->forever('info_size', $size);
    }

    /**
     * File Count
     */
    protected function fileCount()
    {
        $file = with(new Process(explode(' ', 'find . -type f')), function (Process $find) {
            $find->setWorkingDirectory(Storage::path(config('packagist.path')))
                 ->run();

            return $find->isSuccessful() ? $find->getOutput() : '';
        });

        $count = with(new Process(explode(' ', 'wc -l')), function (Process $wc) use ($file) {
            $wc->setInput($file)
               ->run();

            $count = $wc->isSuccessful() ? $wc->getOutput() : 0;

            return number_format(trim($count));
        });

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
