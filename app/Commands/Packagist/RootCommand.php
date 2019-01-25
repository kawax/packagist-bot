<?php

namespace App\Commands\Packagist;

use LaravelZero\Framework\Commands\Command;

use Illuminate\Support\Facades\Storage;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

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
     * @param Client $client
     *
     * @return mixed
     */
    public function handle(Client $client)
    {
        $client->getAsync(config('packagist.root'))
               ->then(function (ResponseInterface $res) {
                   $json = $res->getBody()->getContents();
                   Storage::put(config('packagist.path') . config('packagist.root'), $json);

                   $this->task(config('packagist.root'));
               }, function (RequestException $e) {
                   $this->error($e->getMessage());

                   return;
               })->wait();
    }
}
