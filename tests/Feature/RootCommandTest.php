<?php

namespace Tests\Feature;

use GuzzleHttp\Client;
use Mockery;
use Tests\TestCase;

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
