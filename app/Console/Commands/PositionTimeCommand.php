<?php

namespace App\Console\Commands;

use App\Enums\TripStatus;
use App\Models\Program;
use App\Models\User;
use App\Notifications\Students\PositionTimeNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;

class PositionTimeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:position-time-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Notification To the Students To Catch There GoTrips';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        /**
         * @var User $user
         */

        $date = Date::now()->toDateString();

        $day = Date::now()->dayOfWeekIso;

        $programs = Program::query()
            ->where('day_id', $day)
            ->where(['confirmAttendance1' => true])
            ->whereHas('user', function ($query) use ($date) {
                $query->whereHas('trips', function ($query) use ($date) {
                    $query->where('status', TripStatus::GoTrip)
                        ->whereHas('time', function ($query) use ($date) {
                            $query->where('date', $date);
                        });
                });
            })->get();

        foreach ($programs as $program) {

            $remainTime = Date::now()->diffInMinutes($program->start, false);

            if ($remainTime <= 5 && $remainTime > 1) {

                $user = $program->user_id;

                $user->notify(new PositionTimeNotification());
            }
        }
    }
}
