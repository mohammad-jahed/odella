<?php

use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/firebase', [TripController::class, 'sendNotification']);
Route::get('/test',[TripController::class,'test_go_trips_notification']);
Route::get('/test1',[TripController::class,'test_return_trips_notification']);

Route::get('/socket',function (){
    Artisan::call('websockets:serve');
    return ' ok';
});
