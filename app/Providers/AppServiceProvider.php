<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

use GuzzleHttp\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::macro('publish', function (string $file = 'index.html') {
            Storage::put(config('packagist.path') . $file, $this->render());
        });

        $this->app->singleton(Client::class, function ($app) {
            return new Client(config('packagist.guzzle'));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
