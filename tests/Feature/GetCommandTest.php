<?php

namespace Tests\Feature;

use GuzzleHttp\Pool;
use Tests\TestCase;

class GetCommandTest extends TestCase
{
    public function testGetCommand()
    {
        $this->mock(
            Pool::class,
            function ($mock) {
                $mock->shouldReceive('promise->wait');
            }
        );

        $this->artisan('packagist:get')
             ->assertExitCode(0);
    }
}
