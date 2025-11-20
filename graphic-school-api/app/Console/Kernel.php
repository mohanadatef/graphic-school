<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Database backup - daily at 2 AM
        $schedule->command('backup:database')->dailyAt('02:00');

        // Log cleanup - daily at 3 AM
        $schedule->command('logs:cleanup --days=14')->dailyAt('03:00');

        // Generate reports - daily at 4 AM
        $schedule->command('reports:generate')->dailyAt('04:00');
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
