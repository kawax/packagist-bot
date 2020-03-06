<?php

namespace App\Commands\Packagist;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class IndexCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'packagist:index';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create index';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $last = now();

        view('welcome')->with(
            compact(
                [
                    'last',
                ]
            )
        )->publish('index.html');

        File::copyDirectory(resource_path('public'), Storage::path(''));
    }
}
