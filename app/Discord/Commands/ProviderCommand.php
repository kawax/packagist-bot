<?php

namespace App\Discord\Commands;

use CharlotteDunois\Yasmin\Models\Message;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class ProviderCommand
{
    /**
     * @var string
     */
    public $command = 'provider';

    /**
     * @param  Message  $message
     *
     * @return string
     */
    public function __invoke(Message $message)
    {
        Artisan::call('packagist:root');

        $providers = json_decode(Storage::get(config('packagist.root')));

        $providers = data_get($providers, 'provider-includes');

        $providers = collect($providers)->keys()->implode(PHP_EOL);

        return PHP_EOL.$providers;
    }
}
