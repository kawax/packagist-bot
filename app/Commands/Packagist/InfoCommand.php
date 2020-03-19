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
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
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
                      ->setTimeout(120)
                      ->mustRun()
                      ->getOutput();
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function fileSize(): void
    {
        $this->info('file size');

        $command = 'du -sh';

        $size = rescue(
            fn () => rtrim($this->process($command), ". \n\t"),
            'error'
        );

        $this->line($size);

        cache()->forever('info_size', $size);
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function fileCount(): void
    {
        $this->info('file count');

        $command = 'find . -type f -name "*.json" | wc -l';

        $count = rescue(
            fn () => number_format(trim($this->process($command))),
            0
        );

        $this->line($count);

        cache()->forever('info_count', $count);
    }

    /**
     * Define the command's schedule.
     *
     * @param  Schedule  $schedule
     *
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        //        $schedule->command(static::class, ['--quiet'])
        //                 ->hourlyAt(10);
    }
}
