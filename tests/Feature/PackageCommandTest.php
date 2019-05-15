<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Mockery;

use GuzzleHttp\Pool;

class PackageCommandTest extends TestCase
{
    public function testGetCommand()
    {
        $pool = Mockery::mock(Pool::class);
        $pool->shouldReceive('promise->wait');

        Storage::shouldReceive('get')->andReturn('{"providers":{"test/test":{"sha256":"aaa"}}}');

        Storage::shouldReceive('exists')->andReturn(true);

        $this->artisan('packagist:package test')
             ->assertExitCode(0);
    }
}
