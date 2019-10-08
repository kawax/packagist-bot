<?php

namespace Tests\Feature;

use Tests\TestCase;

class SyncCommandTest extends TestCase
{
    public function testSyncCommandEmpty()
    {
        $this->expectException(\RuntimeException::class);

        config(['packagist.s3.sync' => ''], 60);

        $this->artisan('packagist:sync')
             ->assertExitCode(1);
    }
}
