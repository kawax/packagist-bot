<?php

namespace App\Discord\Commands;

use CharlotteDunois\Yasmin\Models\Message;

use Illuminate\Support\Facades\Artisan;

use Revolution\DiscordManager\Traits\Input;

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
     * @param Message $message
     *
     * @return string
     */
    public function __invoke(Message $message)
    {
        $argv = explode(' ', str_after($message->content, config('services.discord.prefix')));

        $input = $this->input($argv);

        Artisan::queue('packagist:get', [
            'provider' => $input->getArgument('provider'),
        ]);

        return 'Get start... **' . $input->getArgument('provider') . '**';
    }
}
