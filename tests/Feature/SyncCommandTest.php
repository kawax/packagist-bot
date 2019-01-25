<?php

namespace Tests\Feature;

use Tests\TestCase;

class SyncCommandTest extends TestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testSyncCommandEmpty()
    {
        config(['packagist.s3.sync' => ''], 60);

        $this->artisan('packagist:sync')
             ->assertExitCode(1);
    }
}
