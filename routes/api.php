<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\TimeController;
use App\Http\Controllers\TransferPositionController;
use App\Http\Controllers\TransportationLineController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\UniversityController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group([
    'middleware' => ['api', 'set.lang'],
    'prefix' => 'auth'
], function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/adminRegister', [AuthController::class, 'adminRegister']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/refresh', [AuthController::class, 'refresh']);

    Route::get('/profile', [AuthController::class, 'profile']);

    Route::post('/forgetPassword', [AuthController::class, 'forgetPassword']);

    Route::post('/ResetPassword', [AuthController::class, 'resetPassword']);
});


Route::group([
    'middleware' => ['api', 'set.lang']
], function () {

    Route::apiResource('/cities', CityController::class);
    Route::apiResource('/areas', AreaController::class);
    Route::get('/areas/cities/{city}', [AreaController::class, 'areas']);
    Route::apiResource('/locations', LocationController::class);
    Route::apiResource('/subscriptions', SubscriptionController::class);
    Route::apiResource('/transportationLines', TransportationLineController::class);
    Route::apiResource('/transferPositions', TransferPositionController::class);
    Route::get('/transferPositions/transportationLines/{line}', [TransferPositionController::class, 'positions']);
    Route::apiResource('/universities', UniversityController::class);


    Route::group([
        'middleware' => ['auth:api']
    ], function () {

        Route::apiResource('/employees', EmployeeController::class);
        Route::post('/employees/confirmRegistration/{user}', [EmployeeController::class, 'confirmRegistration']);
        Route::apiResource('/students', StudentController::class);
        Route::get('/student/active', [StudentController::class, 'activeStudentsList']);
        Route::get('/student/unActive', [StudentController::class, 'unActiveStudentsList']);
        Route::put('/student/programs/{program}', [StudentController::class, 'confirmAttendance']);
        Route::get('/student/trips/{trip}/positions/{position}', [StudentController::class, 'getAllStudentsInThePosition']);
        Route::apiResource('/supervisors', SupervisorController::class);
        Route::apiResource('/buses', BusController::class);
        Route::apiResource('/drivers', DriverController::class);
        Route::apiResource('/times', TimeController::class);
        Route::apiResource('/programs', ProgramController::class);
        Route::get('/programs', [ProgramController::class, 'userPrograms']);
        Route::apiResource('/trips', TripController::class);
        Route::post('/trips/{trip}/students', [TripController::class, 'addStudents']);
        Route::get('/trips/{trip}/students/{student}', [TripController::class, 'deleteStudent']);

    });


});



