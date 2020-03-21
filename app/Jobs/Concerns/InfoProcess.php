<?php

namespace App\Jobs\Concerns;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

trait InfoProcess
{
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
}
