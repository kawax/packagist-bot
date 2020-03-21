<?php

namespace App\Jobs;

use App\Notifications\SimpleNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class InfoCountJob implements ShouldQueue
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
        $command = 'find . -type f -name "*.json" | wc -l';

        $count = rescue(
            fn () => number_format(trim($this->process($command))),
            0
        );

        cache()->forever('info_count', $count);

        NotifyJob::dispatchNow(new SimpleNotification('count: '.$count));
    }
}
