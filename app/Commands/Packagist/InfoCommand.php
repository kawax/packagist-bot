<?php

namespace App\Commands\Packagist;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
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
    protected $description = 'Update info';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        cache()->forever('info_size', '-');
        cache()->forever('info_count', '-');

        $this->fileSize();
        $this->fileCount();
    }

    /**
     * @param  string  $command
     *
     * @return string
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     */
    protected function process(string $command)
    {
        return Process::fromShellCommandline($command)
                      ->setWorkingDirectory(Storage::path(''))
                      ->setTimeout(300)
                      ->mustRun()
                      ->getOutput();
    }

    protected function fileSize()
    {
        $this->info('file size');

        $size = rescue(
            function () {
                $size = $this->process('du -sh');

                return rtrim($size, ". \n\t");
            },
            'error'
        );

        $this->line($size);

        cache()->forever('info_size', $size);
    }

    protected function fileCount()
    {
        $this->info('file count');

        $count = rescue(
            function () {
                $count = $this->process('find . -type f -name "*.json" | wc -l');

                return number_format(trim($count));
            },
            0
        );

        $this->line($count);

        cache()->forever('info_count', $count);
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
                 ->hourlyAt(10);
    }
}
