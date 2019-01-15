<?php

namespace App\Discord\Commands;

use CharlotteDunois\Yasmin\Models\Message;

use Illuminate\Support\Facades\Artisan;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;

class GetCommand
{
    /**
     * @var string
     */
    public $command = 'get';

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
        $definition = new InputDefinition([
            new InputArgument('provider', InputArgument::REQUIRED),
        ]);

        $argv = collect(explode(' ', $message->content));
        $argv->shift();

        $input = new ArgvInput($argv->toArray(), $definition);

        Artisan::queue('packagist:get', [
            'provider' => trim($input->getArgument('provider')),
        ]);

        return 'Get start... **' . trim($input->getArgument('provider')) . '**';
    }
}
