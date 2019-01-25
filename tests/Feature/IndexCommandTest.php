<?php

namespace Tests\Feature;

use Tests\TestCase;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class IndexCommandTest extends TestCase
{
    public function testIndexCommand()
    {
        View::shouldReceive('make->with->publish')->once()->andReturnNull();
        File::shouldReceive('copyDirectory')->once()->andReturnNull();

        $this->artisan('packagist:index')
             ->assertExitCode(0);
    }
}
