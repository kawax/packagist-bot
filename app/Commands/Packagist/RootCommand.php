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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @param Client $client
     *
     * @return mixed
     */
    public function handle(Client $client)
    {
        $client->getAsync('packages.json')
               ->then(function (ResponseInterface $res) {
                   $json = $res->getBody()->getContents();
                   Storage::put(config('packagist.path') . 'packages.json', $json);

                   $this->task('packages.json');
               }, function (RequestException $e) {
                   $this->error($e->getMessage());
               })->wait();
    }
}
