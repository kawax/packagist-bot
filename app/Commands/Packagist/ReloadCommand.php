<?php

namespace App\Commands\Packagist;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use Revolution\DiscordManager\Facades\RestCord;

class ReloadCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'packagist:reload';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('packagist:root');
        $this->call('packagist:get');
        $this->call('packagist:index');
        $result = $this->call('packagist:sync');
        //        $this->call('packagist:purge');

        if ($result === 0) {
            $content = 'Reload completed!';
        } else {
            $content = 'Reload failed?';
        }

        RestCord::channel()->createMessage([
            'content'    => $content,
            'channel.id' => (int)config('services.discord.channel'),
        ]);
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        $schedule->command(static::class)->hourlyAt(50);
    }
}
