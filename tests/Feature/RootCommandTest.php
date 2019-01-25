<?php

namespace Tests\Feature;

use Tests\TestCase;
use Mockery;

use GuzzleHttp\Client;

class RootCommandTest extends TestCase
{
    /**
     * @var Mockery\MockInterface
     */
    protected $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = Mockery::mock(Client::class);

        app()->instance(Client::class, $this->client);
    }

    public function testRootCommand()
    {
        $this->client->shouldReceive('getAsync->then->wait')
                     ->once()
                     ->andReturnNull();

        $this->artisan('packagist:root')
             ->assertExitCode(0);
    }
}
