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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Throwable
     */
    public function handle()
    {
        throw_if(
            blank(config('packagist.s3.sync')),
            \Exception::class,
            'S3_SYNC is empty'
        );

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
}
