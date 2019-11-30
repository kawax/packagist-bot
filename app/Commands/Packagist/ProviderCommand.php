<?php

namespace App\Commands\Packagist;

use LaravelZero\Framework\Commands\Command;

use Illuminate\Support\Facades\Storage;

class ProviderCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'packagist:provider';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Download selected provider';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('packagist:root');

        $providers = json_decode(Storage::get(config('packagist.root')));

        $providers = data_get($providers, 'provider-includes');

        $provider = $this->choice('Select Provider', collect($providers)->keys()->toArray());

        $this->info($provider);

        if (filled($provider)) {
            $this->call(
                'packagist:get',
                [
                    'provider' => $provider,
                ]
            );
        }
    }
}
