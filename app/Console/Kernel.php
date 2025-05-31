<?php

namespace Alison\ProjectManagementAssistant\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Запускаємо команду щодня о 8:00 ранку для надсилання сповіщень про початок подій
        $schedule->command('app:send-event-start-notifications')
            ->dailyAt('08:00')
            ->timezone('Europe/Kiev')
            ->onOneServer()
            ->withoutOverlapping()
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
