<?php

namespace Tests\Feature;

use Tests\TestCase;

use Mockery as m;

use Aws\CloudFront\CloudFrontClient;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\AnonymousNotifiable;
use App\Notifications\SimpleNotification;

class PurgeCommandTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface
     */
    protected $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = m::mock(CloudFrontClient::class);

        app()->instance(CloudFrontClient::class, $this->client);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testPurge()
    {
        Notification::fake();

        Cache::shouldReceive('lock->get')->andReturn(true);

        $this->client->shouldReceive('createInvalidation')
                     ->andReturn([
                         'Invalidation' => [
                             'Status' => 'InProgress',
                         ],
                     ]);

        $this->artisan('packagist:purge')
             ->assertExitCode(0);

        Notification::assertSentTo(
            new AnonymousNotifiable,
            SimpleNotification::class,
            function ($notification, $channels) {
                return str_contains($notification->content, 'InProgress');
            }
        );
    }

    public function testPurgeLock()
    {
        Notification::fake();

        Cache::shouldReceive('lock->get')->andReturn(false);

        $this->client->shouldReceive('createInvalidation')
                     ->never();

        $this->artisan('packagist:purge')
             ->assertExitCode(1);

        Notification::assertSentTo(
            new AnonymousNotifiable,
            SimpleNotification::class,
            function ($notification, $channels) {
                return str_contains($notification->content, 'Purge rate limit');
            }
        );
    }

    public function testPurgeException()
    {
        config(['packagist.cloudfront.dist' => ''], 60);

        $this->client->shouldReceive('createInvalidation')
                     ->never();

        $this->artisan('packagist:purge')
             ->expectsOutput('Set CloudFront Distribution ID')
             ->assertExitCode(1);
    }
}
