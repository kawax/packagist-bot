<?php

namespace App\Commands\Packagist;

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
    protected $description = 'Syncing data with S3';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Throwable
     *
     * @codeCoverageIgnore
     */
    public function handle()
    {
        throw_if(
            blank(config('packagist.s3.sync')),
            \RuntimeException::class,
            'S3_SYNC is empty'
        );

        if (! app()->isProduction()) {
            return;
        }

        Process::fromShellCommandline(config('packagist.s3.sync'))
               ->setWorkingDirectory(Storage::path(''))
               ->setTimeout(config('packagist.s3.timeout'))
               ->run(
                   function ($type, $buffer) {
                       if ($type === Process::ERR) {
                           $this->error($buffer);
                       } else {
                           $this->line($buffer);
                       }
                   }
               );
    }
}
