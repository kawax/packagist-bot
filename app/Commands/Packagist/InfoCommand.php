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
     * @var string
     */
    protected $path;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->path = Storage::path(config('packagist.path'));

        $this->fileSize();
        $this->fileCount();
    }

    /**
     * File Size
     */
    protected function fileSize()
    {
        $size = rescue(function () {
            $size = $this->process('du -sh');

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
        $count = rescue(function () {
            $count = $this->process('find . -type f -name "*.json" | wc -l');

            return number_format(trim($count));
        }, 0);

        $this->info($count);

        cache()->forever('info_count', $count);
    }

    /**
     * @param string $command
     *
     * @return string
     */
    protected function process(string $command)
    {
        return Process::fromShellCommandline($command)
                      ->setWorkingDirectory($this->path)
                      ->mustRun()
                      ->getOutput();
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
