<?php

namespace Tests\Feature;

use GuzzleHttp\Pool;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PackageCommandTest extends TestCase
{
    public function testGetCommand()
    {
        $this->mock(
            Pool::class,
            function ($mock) {
                $mock->shouldReceive('promise->wait');
            }
        );

        Storage::shouldReceive('get')->andReturn('{"providers":{"test/test":{"sha256":"aaa"}}}');

        Storage::shouldReceive('exists')->andReturn(true);

        $this->artisan('packagist:package test')
             ->assertExitCode(0);
    }
}
