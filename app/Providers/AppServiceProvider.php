<?php

namespace App\Providers;

use Aws\CloudFront\CloudFrontClient;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
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

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->macro('publish', function (string $file = 'index.html') {
            Storage::put($file, $this->render());
        });
    }
}
