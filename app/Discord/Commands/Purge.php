<?php

namespace App\Discord\Commands;

use CharlotteDunois\Yasmin\Models\Message;
use Illuminate\Support\Facades\Artisan;

class Purge
{
    /**
     * @var string
     */
    public $command = 'purge';

    /**
     * @var bool
     */
    public $hidden = true;

    /**
     * @param  Message  $message
     *
     * @return string
     */
    public function __invoke(Message $message)
    {
        Artisan::queue('packagist:purge');

        return '';
    }
}
