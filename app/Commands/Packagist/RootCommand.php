<?php

namespace App\Commands\Packagist;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Psr\Http\Message\ResponseInterface;

class RootCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'packagist:root';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Download root packages';

    /**
     * Execute the console command.
     *
     * @param  Client  $client
     *
     * @return mixed
     */
    public function handle(Client $client)
    {
        $client->getAsync(config('packagist.root'))
               ->then(
                   function (ResponseInterface $res) {
                       $this->task(config('packagist.root'));

                       Storage::put(
                           config('packagist.root'),
                           $res->getBody()->getContents()
                       );
                   },
                   function (RequestException $e) {
                       $this->error($e->getMessage());
                   }
               )->wait();
    }
}
