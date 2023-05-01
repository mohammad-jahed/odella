<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\DailyReservationController;
use App\Http\Controllers\DaysController;
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


// Authentication routes
Route::group([
    'middleware' => ['api', 'set.lang'],
    'prefix'     => 'auth'
], function () {
    /**
     * Login route.
     */
    Route::post('/login', [AuthController::class, 'login']);

    /**
     * Register a new user.
     */
    Route::post('/register', [AuthController::class, 'register']);

    /**
     * Register a new admin user.
     */
    Route::post('/adminRegister', [AuthController::class, 'adminRegister']);

    /**
     * Logout the current user.
     */
    Route::post('/logout', [AuthController::class, 'logout']);

    /**
     * Refresh the token for the current user.
     */
    Route::post('/refresh', [AuthController::class, 'refresh']);

    /**
     * Get the user's profile.
     */
    Route::get('/profile', [AuthController::class, 'profile']);

    /**
     * Send a password reset email.
     */
    Route::post('/forgetPassword', [AuthController::class, 'forgetPassword']);

    /**
     * Reset the user's password.
     */
    Route::post('/ResetPassword', [AuthController::class, 'resetPassword']);
});

// Public routes
Route::group([
    'middleware' => ['api', 'set.lang']
], function () {
    /**
     * Resource routes for cities.
     */
    Route::apiResource('/cities', CityController::class);

    /**
     * Resource routes for areas.
     */
    Route::apiResource('/areas', AreaController::class);

    /**
     * Get all areas for a specific city.
     */
    Route::get('/areas/cities/{city}', [AreaController::class, 'areas']);

    /**
     * Resource routes for locations.
     */
    Route::apiResource('/locations', LocationController::class);

    /**
     * Resource routes for subscriptions.
     */
    Route::apiResource('/subscriptions', SubscriptionController::class);

    /**
     * Resource routes for transportation lines.
     */
    Route::apiResource('/transportationLines', TransportationLineController::class);

    /**
     * Resource routes for transfer positions.
     */
    Route::apiResource('/transferPositions', TransferPositionController::class);

    /**
     * Get all transfer positions for a specific transportation line.
     */
    Route::get('/transferPositions/transportationLines/{line}', [TransferPositionController::class, 'positions']);

    /**
     * Resource routes for universities.
     */
    Route::apiResource('/universities', UniversityController::class);

    /**
     * Creates a new daily reservation for a specific trip.
     */
    Route::post('/dailyReservations/trips/{trip}', [DailyReservationController::class, 'dailyReservation']);

    /**
     * Resource routes for days.
     */
    Route::apiResource('/days', DaysController::class);



// Authenticated routes
    Route::group([
        'middleware' => ['auth:api']
    ], function () {
        /**
         * Resource routes for employees.
         */
        Route::apiResource('/employees', EmployeeController::class);

        /**
         * Confirms the registration of a new student.
         */
        Route::post('/employees/confirmRegistration/{user}', [EmployeeController::class, 'confirmRegistration']);

        /**
         * Resource routes for students.
         */
        Route::apiResource('/students', StudentController::class);

        /**
         * Retrieves a list of all active students.
         */
        Route::get('/student/active', [StudentController::class, 'activeStudentsList']);

        /**
         * Retrieves a list of all inactive students.
         */
        Route::get('/student/unActive', [StudentController::class, 'unActiveStudentsList']);

        /**
         * Confirms the attendance of a student for a specific program.
         */
        Route::put('/student/programs/{program}', [StudentController::class, 'confirmAttendance']);

        /**
         * Retrieves a list of all students in a specific position for a specific trip.
         */
        Route::get('/student/trips/{trip}/positions/{position}', [StudentController::class, 'getAllStudentsInThePosition']);

        /**
         * Resource routes for supervisors.
         */
        Route::apiResource('/supervisors', SupervisorController::class);

        /**
         * Approves a daily reservation made by a supervisor.
         */
        Route::get('/supervisor/approve/{reservation}', [SupervisorController::class, 'approveReservation']);

        /**
         * Denies a daily reservation made by a supervisor.
         */
        Route::get('/supervisor/deny/{reservation}', [SupervisorController::class, 'denyReservation']);

        /**
         * Retrieves the daily reservation for a specific trip.
         */
        Route::get('/dailyReservations/trips/{trip}', [DailyReservationController::class, 'getDailyReservation']);

        /**
         * Resource routes for buses.
         */
        Route::apiResource('/buses', BusController::class);

        /**
         * Resource routes for drivers.
         */
        Route::apiResource('/drivers', DriverController::class);

        /**
         * Retrieves a list of all drivers who are also bus drivers.
         */
        Route::get('/busDrivers', [DriverController::class, 'getBusDrivers']);

        /**
         * Resource routes for times.
         */
        Route::apiResource('/times', TimeController::class);

        /**
         * Resource routes for programs.
         */
        Route::apiResource('/programs', ProgramController::class);

        /**
         * Retrieves a list of programs associated with the current user.
         */
        Route::get('/programs', [ProgramController::class, 'userPrograms']);

        /**
         * Resource routes for trips.
         */
        Route::apiResource('/trips', TripController::class);

        /**
         * Get all trips for a specific transportation line.
         */
        Route::get('/trips/line/{transportationLine}', [TripController::class, 'tripsLine']);

        /**
         * Adds one or more students to a specific trip.
         */
        Route::post('/trips/{trip}/students', [TripController::class, 'addStudents']);

        /**
         * Deletes a specific student from a specific trip.
         */
        Route::get('/trips/{trip}/students/{student}', [TripController::class, 'deleteStudent']);

        Route::get('/trips/students/student' , [TripController::class, 'getStudentTrips']);



        Route::apiResource('/claims', ClaimController::class);

    });


});



