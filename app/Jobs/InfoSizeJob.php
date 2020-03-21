<?php

namespace App\Jobs;

use App\Notifications\SimpleNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class InfoSizeJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Concerns\InfoProcess;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $command = 'du -sh';

        $size = rescue(
            fn () => rtrim($this->process($command), ". \n\t"),
            'error'
        );

        cache()->forever('info_size', $size);

        NotifyJob::dispatchNow(new SimpleNotification('size: '.$size));
    }
}
