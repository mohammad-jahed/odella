<?php

namespace App\Console\Commands;

use App\Enums\Status;
use App\Models\User;
use App\Notifications\Students\StopRegistrationNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;

class StopStudentRegistrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:stop-student-registration-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //
        /**
         * @var User[] $students
         * @var User $student
         */
        $students = User::role('Student')
            ->where('expiredSubscriptionDate', '<=', Date::now()->subMonth())
            ->get();

        foreach ($students as $student) {

            $date = Date::now()->diffInDays($student->expiredSubscriptionDate, false);

            if ($date == 0) {
                $student->status = Status::UnActive;
                $student->save();
                $student->notify(new StopRegistrationNotification());
            }
        }
    }
}
