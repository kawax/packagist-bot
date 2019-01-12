<?php

namespace App\Commands\Packagist;

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
     * @param string $command
     *
     * @return string
     * @throws
     */
    protected function process(string $command)
    {
        return Process::fromShellCommandline($command)
                      ->setWorkingDirectory(Storage::path(config('packagist.path')))
                      ->mustRun()
                      ->getOutput();
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
}