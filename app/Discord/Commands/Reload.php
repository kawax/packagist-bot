<?php

namespace App\Discord\Commands;

use CharlotteDunois\Yasmin\Models\Message;
use Illuminate\Support\Facades\Artisan;

class Reload
{
    /**
     * @var string
     */
    public $command = 'reload';

    /**
     * @param  Message  $message
     *
     * @return string
     */
    public function __invoke(Message $message)
    {
        Artisan::queue('packagist:reload');

        return 'Reload start...';
    }
}
