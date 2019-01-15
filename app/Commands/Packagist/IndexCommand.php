<?php

namespace App\Commands\Packagist;

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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $last = now();

        view('welcome')->with(compact([
            'last',
        ]))->publish('index.html');

        view('robots')->publish('robots.txt');
    }
}
