<?php

namespace App\Discord\Commands;

class PurgeShort extends Purge
{
    /**
     * @var string
     */
    public $command = 'p';

    /**
     * @var bool
     */
    public $hidden = true;
}
