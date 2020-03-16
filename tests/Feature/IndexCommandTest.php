<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class IndexCommandTest extends TestCase
{
    public function testIndexCommand()
    {
        View::shouldReceive('make->with->publish')->once()->andReturnNull();
        File::shouldReceive('copyDirectory')->once()->andReturnNull();

        $this->artisan('packagist:index')
             ->assertExitCode(0);
    }

    public function testViewMacroPublish()
    {
        Storage::shouldReceive('put')->once();

        view('welcome')->with(['last' => now()])->publish();
    }
}
