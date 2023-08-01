<?php

use App\Http\Controllers\AlgorithmInputController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\DailyReservationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DaysController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LostFoundController;
use App\Http\Controllers\NotificationController;
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
    'prefix' => 'auth'
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
     * Check the confirmation code that send to user email.
     */
    Route::post('/EmailConfirmation', [AuthController::class, 'emailConfirmation']);

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
     * Get current trips
     */
    Route::post('/currentTrips', [TripController::class, 'current_trips']);

    /**
     * Get all today trips for daily reservation.
     */
    Route::post('/trip/todayTrips', [TripController::class, 'todayTrips']);

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
         * Update Student Subscription By Employee
         */
        Route::put('/employees/subscriptions/{student}', [EmployeeController::class, 'updateStudentSubscription']);

        /**
         * Adding Student Payment
         */
        Route::post('/employees/payments/{student}',[EmployeeController::class,'addingStudentPayment']);
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
         * get all students outer specif trip
         */
        Route::get('/student/trips/outerTrip/{trip}', [StudentController::class, 'getStudentsOuterTrip']);

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
         * Student Confirm Attendance With QR Code made by a supervisor.
         */
        Route::post('/supervisor/qrcode/confirm/trip/{trip}/user/{user}',[SupervisorController::class, 'qrConfirmAttendance']);

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

        /**
         * Get all trips for a specific student.
         */
        Route::get('/trips/students/student', [TripController::class, 'getStudentTrips']);

        /**
         * Get all go trips.
         */
        Route::get('/trip/goTrips', [TripController::class, 'getGoTrips']);

        /**
         * Get all return trips.
         */
        Route::get('/trip/returnTrips', [TripController::class, 'getReturnTrips']);

        /**
         * Get all weekly trips
         */
        Route::get('/trip/weeklyTrips', [TripController::class, 'getPreviousWeekStudentTrips']);

        /**
         * Get the current trip for the current supervisor.
         */
        Route::post('/supervisor/trip',[TripController::class,'supervisor_current_trip']);

        /**
         * Get all weekly trips before today.
         */
        Route::get('/trip/weeklyTripsBeforeToday', [TripController::class, 'getWeeklyTripsBeforeToday']);

//        /**
//         * Get all today trips for daily reservation.
//         */
//        Route::post('/trip/todayTrips', [TripController::class, 'todayTrips']);


        /**
         * Resource routes for claims.
         */
        Route::apiResource('/claims', ClaimController::class);

        /**
         * Resource routes for lost&founds.
         */
        Route::apiResource('/lost_found', LostFoundController::class);

        /**
         * Get All Lost&Founds On The Logged In Supervisor Trip
         */
        Route::get('/lost_founds/supervisor', [LostFoundController::class, 'getLostAndFoundsOnLoggedInSupervisorTrip']);

        /**
         * Adds Evaluation to a specific trip.
         */
        Route::post('/evaluation/trip/{trip}', [EvaluationController::class, 'store']);

        /**
         * Get all Evaluations for a specific trip.
         */
        Route::get('/evaluation/trip/{trip}', [EvaluationController::class, 'trip_evaluations']);

        /**
         * Resource routes for Evaluations.
         */
        Route::apiResource('/evaluation', EvaluationController::class);

        /**
         * Supervisor position update for trip tracking.
         */
        Route::post('/update/position',[SupervisorController::class, 'updatePosition']);

        /**
         * Get all notifications for the logged-In user.
         */
        Route::get('user/notification',[NotificationController::class, 'index']);

        /**
         * Get UnRead notifications for the logged-In user.
         */
        Route::get('user/unread_notification',[NotificationController::class, 'get_unread_notifications']);

        /**
         * Get A Specific notifications by id.
         */
        Route::get('user/notification/{notification}',[NotificationController::class, 'show']);

        /**
         * Make A Specific notifications Read.
         */
        Route::post('user/make_read_notification/{notification}',[NotificationController::class, 'make_notification_read']);

        /**
         * Make All notifications Read.
         */
        Route::get('user/make_all_read_notifications',[NotificationController::class, 'make_all_notification_read']);

        /**
         * Delete A Specific notifications by id.
         */
        Route::delete('user/notification/{notification}',[NotificationController::class, 'destroy']);

        /**
         *  Store Algorithm Inputs for user.
         */

        Route::post('algorithmInput', [AlgorithmInputController::class, 'store']);

        /**
         * Dashboard
         */

        Route::get('dashboard/studentsByDayAndUniversities', [DashboardController::class, 'studentsByDayAndUniversity']);

        Route::get('dashboard/tripsByDayAndUniversities', [DashboardController::class, 'tripsByDayAndUniversities']);

        Route::get('dashboard/studentsByLine', [DashboardController::class, 'studentsByLine']);

        Route::get('dashboard/tripClaimStatistics', [DashboardController::class, 'tripClaimStatistics']);

        Route::get('dashboard/tripEvaluationsStatistics', [DashboardController::class, 'tripEvaluationsStatistics']);

        Route::get('dashboard/tripReservationsStatistics', [DashboardController::class, 'dailyReservationStatistics']);



    });


});



