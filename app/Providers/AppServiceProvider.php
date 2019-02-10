<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

use GuzzleHttp\Client;
use Aws\CloudFront\CloudFrontClient;

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
            Storage::put($file, $this->render());
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client::class, function ($app) {
            return new Client(config('packagist.guzzle'));
        });

        $this->app->singleton(CloudFrontClient::class, function ($app) {
            return new CloudFrontClient([
                'credentials' => [
                    'key'    => config('packagist.aws.key'),
                    'secret' => config('packagist.aws.secret'),
                ],
                'region'      => 'us-east-1',
                'version'     => 'latest',
            ]);
        });
    }
}
