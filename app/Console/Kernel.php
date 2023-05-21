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
        // $schedule->command('inspire')->hourly();
        $schedule->command('app:position-time-command')->everyThreeMinutes()->between('07:00', '12:00');
        $schedule->command('app:return-time-command')->everyFiveMinutes()->between('12:00', '18:00');
        $schedule->command('app:generate-trips-command')->weekly();
        $schedule->command('app:expired-subscription-command')->weekly();
        $schedule->command('app:stop-student-registration-command')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
