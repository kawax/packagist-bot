<?php

namespace Tests\Feature;

use Tests\TestCase;
use GuzzleHttp\Pool;

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
