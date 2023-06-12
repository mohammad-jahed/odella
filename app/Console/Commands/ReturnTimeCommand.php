<?php

namespace App\Console\Commands;

use App\Enums\TripStatus;
use App\Models\Program;
use App\Notifications\Students\ReturnTimeNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;

class ReturnTimeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:return-time-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'end Notification To the Students To Catch There ReturnTrips';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $date = Date::now()->toDateString();

        $day = Date::now()->dayOfWeek;

        $programs = Program::query()
            ->where('day_id', $day)
            ->where(['confirmAttendance2' => true])
            ->whereHas('user', function ($query) use ($date) {
                $query->whereHas('trips', function ($query) use ($date) {
                    $query->where('status', TripStatus::ReturnTrip)
                        ->whereHas('time', function ($query) use ($date) {
                            $query->where('date', $date);
                        });
                });
            })->get();

        foreach ($programs as $program) {

            $remainTime = Date::now()->diffInMinutes($program->end, false);

            if ($remainTime > 0 && $remainTime <= 15) {

                $user = $program->user_id;

                $user->notify(new ReturnTimeNotification($remainTime));
            }

        }
    }
}
