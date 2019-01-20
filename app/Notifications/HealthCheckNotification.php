<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

use Carbon\Carbon;

class HealthCheckNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var integer
     */
    public $last;

    /**
     * Create a new notification instance.
     *
     * @param int $last
     *
     * @return void
     */
    public function __construct(int $last)
    {
        $this->last = $last;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return [DiscordChannel::class];
    }

    public function toDiscord($notifiable)
    {
        $content = '☠️Health Check Error' . PHP_EOL;
        $content .= sprintf('```Last-Modified: %s```', Carbon::createFromTimestamp($this->last)->toIso8601String());

        return DiscordMessage::create($content);
    }
}
