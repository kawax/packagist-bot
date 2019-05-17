<?php

namespace App\Discord\Commands;

use CharlotteDunois\Yasmin\Models\Message;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;

use Revolution\DiscordManager\Concerns\Input;

class GetCommand
{
    use Input;

    /**
     * @var string
     */
    public $command = 'get {provider}';

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
        $argv = explode(' ', Str::after($message->content, config('services.discord.prefix')));

        $input = $this->input($argv);

        Artisan::queue('packagist:get', [
            'provider' => $input->getArgument('provider'),
        ]);

        return 'Get start... **'.$input->getArgument('provider').'**';
    }
}
