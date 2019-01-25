<?php

namespace Tests\Feature;

use Tests\TestCase;

class InfoCommandTest extends TestCase
{
    public function testInfoCommand()
    {
        $this->artisan('packagist:info')
             ->expectsOutput('file size')
             ->expectsOutput('file count')
             ->assertExitCode(0);
    }
}
