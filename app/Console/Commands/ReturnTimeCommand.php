<?php

namespace App\Console\Commands;

use App\Enums\TripStatus;
use App\Models\Program;
use App\Models\TransferPosition;
use App\Models\Trip;
use App\Models\User;
use App\Notifications\Students\ReturnTimeNotification;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
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
    protected $description = 'end Notifications To the Students To Catch There ReturnTrips';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        /**
         * @var array $trip_ids
         */
        /**
         * @var array $user_ids
         */
        /**
         * @var array $position_ids
         */
        /**
         * @var User $user
         */
        $date = Date::now()->toDateString();
        $trips = Trip::query()->where('status', TripStatus::ReturnTrip)
            ->whereHas('time',
                function (Builder $builder1) use ($date) {
                    $builder1->where('date', '=', $date);
                })->get();

        foreach ($trips as $trip)
            $trip_ids[] = $trip->id;

        $users = User::query()->whereHas('trips',
            function (Builder $builder1) use ($trip_ids) {
                $builder1->wherein('trip_id', $trip_ids);
            })->get();

        foreach ($users as $user)
            $user_ids[] = $user->id;

        $positions = TransferPosition::query()->whereHas(
            'trips', function (Builder $builder) use ($trip_ids) {
            $builder->wherein('trip_id', $trip_ids);
        })->get();

        foreach ($positions as $position)
            $position_ids[] = $position->id;


        $day = Date::now()->dayOfWeek;

        $programs = Program::query()->where('day_id', $day)
            ->where(['confirmAttendance2' => true])
            ->wherein('user_id', $user_ids)
            ->wherein('transfer_position_id', $position_ids)
            ->get();

        foreach ($programs as $program) {
            $remainTime = Date::now()->diffInMinutes($program->end, false);
            if ($remainTime <= 15 && $remainTime > 10) {

                $user = User::query()->where('id', $program->user_id)->first();
                $user->notify(new ReturnTimeNotification($user, $remainTime));
            }
            if ($remainTime <= 10 && $remainTime > 5) {

                $user = User::query()->where('id', $program->user_id)->first();
                $user->notify(new ReturnTimeNotification($user, $remainTime));
            }
            if ($remainTime <= 5 && $remainTime > 0) {

                $user = User::query()->where('id', $program->user_id)->first();
                $user->notify(new ReturnTimeNotification($user, $remainTime));
            }

        }
    }
}
