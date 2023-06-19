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
//        $schedule->command('app:position-time-command')->everyThreeMinutes()->between('07:00', '12:00');
//        $schedule->command('app:return-time-command')->everyFiveMinutes()->between('12:00', '18:00');
//        $schedule->command('app:generate-trips-command')->weeklyOn(5, '01:00');
//        $schedule->command('app:expired-subscription-command')->weeklyOn(5, '01:00');
//        $schedule->command('app:stop-student-registration-command')->dailyAt('00:00');
        //$schedule->command('app:test-command')->everyMinute();

        $schedule->command('app:position-time-command')->everyMinute();
        $schedule->command('app:return-time-command')->everyMinute();
        $schedule->command('app:generate-trips-command')->everyMinute();
        $schedule->command('app:expired-subscription-command')->everyMinute();
        $schedule->command('app:stop-student-registration-command')->everyMinute();
        $schedule->command('app:test-command')->everyMinute();
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
