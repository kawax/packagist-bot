<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class HashErrorNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $url;

    /**
     * Create a new notification instance.
     *
     * @param $title
     * @param $url
     *
     * @return void
     */
    public function __construct(string $title, string $url)
    {
        $this->title = $title;
        $this->url = config('packagist.guzzle.base_uri').$url;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return [DiscordChannel::class];
    }

    public function toDiscord($notifiable)
    {
        $embed = [
            'title' => $this->title,
            'url'   => $this->url,
            'color' => 12386329,
        ];

        return DiscordMessage::create('⚠️Hash error', $embed);
    }
}
