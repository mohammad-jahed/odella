<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\Students\ExpiredSubscriptionNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;

class ExpiredSubscriptionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expired-subscription-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A Daily Command To Send Notifications To Student With Near  Subscription Expiration Date ';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        /**
         * @var User $students
         */
        $students = User::role('Student')
            ->where('expiredSubscriptionDate', '<=', Date::now()->subMonth())
            ->get();

        foreach ($students as $student) {

            $date = Date::now()->diffInDays($student->expiredSubscriptionDate, false);

            if ($date <= 15) {

                $student->notify(new ExpiredSubscriptionNotification($date));
            }
        }

    }
}
