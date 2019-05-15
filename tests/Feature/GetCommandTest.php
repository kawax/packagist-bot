<?php

namespace Tests\Feature;

use Tests\TestCase;
use Mockery;

use GuzzleHttp\Pool;

class GetCommandTest extends TestCase
{
    public function testGetCommand()
    {
        $pool = Mockery::mock(Pool::class);
        $pool->shouldReceive('promise->wait');

        $this->artisan('packagist:get')
             ->assertExitCode(0);
    }
}
