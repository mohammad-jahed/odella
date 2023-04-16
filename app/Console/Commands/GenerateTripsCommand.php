<?php

namespace App\Console\Commands;

use App\Models\Time;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;

class GenerateTripsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-trips-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        /**
         * @var Trip $trip;
         * @var Time $time;
         */
        $startOfWeek = now()->subWeek()->startOfWeek();
        $endOfWeek = now()->subWeek()->endOfWeek();
        $trips = Trip::query()->whereHas('time',
            function (Builder $builder) use ($endOfWeek, $startOfWeek) {
                $builder->whereBetween('date',[$startOfWeek, $endOfWeek]);
            }
        )->get();
        foreach ($trips as $trip){
            $newTrip = $trip->replicate();
            $att = [
                'date' => Carbon::make($trip->time->date)->addWeek(),
                'start' => $trip->time->start
            ];
            $time = Time::query()->create($att);
            $newTrip->time_id = $time->id;
            $newTrip->save();
        }
    }
}
