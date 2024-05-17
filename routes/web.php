<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AirportController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\FlightScheduleController;
use App\Http\Controllers\AircraftController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\CustomerLoginController;


// Default route
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Authenticated routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Dashboard route
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});


//own routes login routes
Route::get('admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AdminLoginController::class, 'login']);

Route::get('customer/login', [CustomerLoginController::class, 'showLoginForm'])->name('customer.login');
Route::post('customer/login', [CustomerLoginController::class, 'login']);

Route::prefix('v1')->group(function () {
    // Routes accessible only by authenticated admins
    Route::middleware('auth:admin')->group(function () {
        // Flight Routes
        Route::post('create-flight', [\App\Http\Controllers\FlightController::class, 'createFlight']);
        Route::post('edit-flight', [\App\Http\Controllers\FlightController::class, 'editFlight']);
        Route::post('delete-flight', [\App\Http\Controllers\FlightController::class, 'deleteFlight']);

        // Payment Routes
        Route::post('payment-list', [\App\Http\Controllers\PaymentController::class, 'paymentList']);
        Route::post('payment-filter', [\App\Http\Controllers\PaymentController::class, 'paymentFilter']);

        // Airport Routes
        Route::post('airport-create', [\App\Http\Controllers\AirportController::class, 'createAirport']);
        Route::post('airport-edit', [\App\Http\Controllers\AirportController::class, 'editAirport']);
        Route::post('airport-delete', [\App\Http\Controllers\AirportController::class, 'deleteAirport']);

        // Seat Routes
        Route::post('seat-create', [\App\Http\Controllers\SeatController::class, 'createSeat']);
        Route::post('seat-edit', [\App\Http\Controllers\SeatController::class, 'editSeat']);
        Route::post('seat-delete', [\App\Http\Controllers\SeatController::class, 'deleteSeat']);

        // Flight Schedule Routes
        Route::post('flight-schedule-create', [\App\Http\Controllers\FlightScheduleController::class, 'createFlightSchedule']);
        Route::post('flight-schedule-edit', [\App\Http\Controllers\FlightScheduleController::class, 'editFlightSchedule']);
        Route::post('flight-schedule-delete', [\App\Http\Controllers\FlightScheduleController::class, 'deleteFlightSchedule']);

        // Aircraft Routes
        Route::post('aircraft-create', [\App\Http\Controllers\AircraftController::class, 'createAircraft']);
        Route::post('aircraft-edit', [\App\Http\Controllers\AircraftController::class, 'editAircraft']);
        Route::post('aircraft-delete', [\App\Http\Controllers\AircraftController::class, 'deleteAircraft']);

        // Admin Routes
        Route::post('admin-list', [\App\Http\Controllers\AdminController::class, 'adminList']);
        Route::post('admin-details', [\App\Http\Controllers\AdminController::class, 'adminDetails']);
        Route::post('admin-create', [\App\Http\Controllers\AdminController::class, 'createAdmin']);
        Route::post('admin-edit', [\App\Http\Controllers\AdminController::class, 'editAdmin']);
        Route::post('admin-delete', [\App\Http\Controllers\AdminController::class, 'deleteAdmin']);
        Route::post('admin-filter', [\App\Http\Controllers\AdminController::class, 'adminFilter']);

        // Customer Routes
        Route::post('customer-list', [\App\Http\Controllers\CustomerController::class, 'customerList']);
        Route::post('customer-details', [\App\Http\Controllers\CustomerController::class, 'customerDetails']);
        Route::post('customer-create', [\App\Http\Controllers\CustomerController::class, 'createCustomer']);
        Route::post('customer-edit', [\App\Http\Controllers\CustomerController::class, 'editCustomer']);
        Route::post('customer-delete', [\App\Http\Controllers\CustomerController::class, 'deleteCustomer']);
        Route::post('customer-filter', [\App\Http\Controllers\CustomerController::class, 'customerFilter']);
    });

    // Routes accessible by both admins and customers
    Route::middleware('adminOrCustomer')->group(function () {
        // Flight Routes
        Route::post('flight-list', [\App\Http\Controllers\FlightController::class, 'flightList']);
        Route::post('flight-details/{id}', [\App\Http\Controllers\FlightController::class, 'flightDetails']);
        Route::post('flight-filter', [\App\Http\Controllers\FlightController::class, 'flightFilter']);

        // Booking Routes
        Route::post('booking-list', [\App\Http\Controllers\BookingController::class, 'bookingList']);
        Route::post('booking-details', [\App\Http\Controllers\BookingController::class, 'bookingDetails']);
        Route::post('booking-create', [\App\Http\Controllers\BookingController::class, 'createBooking']);
        Route::post('booking-edit', [\App\Http\Controllers\BookingController::class, 'editBooking']);
        Route::post('booking-delete', [\App\Http\Controllers\BookingController::class, 'deleteBooking']);
        Route::post('booking-filter', [\App\Http\Controllers\BookingController::class, 'bookingFilter']);

        // Airport Routes
        Route::get('airport-list', [\App\Http\Controllers\AirportController::class, 'airportList']);
        Route::post('airport-details', [\App\Http\Controllers\AirportController::class, 'airportDetails']);
        Route::post('airport-filter', [\App\Http\Controllers\AirportController::class, 'airportFilter']);

        // Seat Routes
        Route::post('seat-list', [\App\Http\Controllers\SeatController::class, 'seatList']);
        Route::post('seat-details', [\App\Http\Controllers\SeatController::class, 'seatDetails']);
        Route::post('seat-filter', [\App\Http\Controllers\SeatController::class, 'seatFilter']);

        // Flight Schedule Routes
        Route::post('flight-schedule-list', [\App\Http\Controllers\FlightScheduleController::class, 'flightScheduleList']);
        Route::post('flight-schedule-details', [\App\Http\Controllers\FlightScheduleController::class, 'flightScheduleDetails']);
        Route::post('flight-schedule-filter', [\App\Http\Controllers\FlightScheduleController::class, 'flightScheduleFilter']);

        // Aircraft Routes
        Route::post('aircraft-list', [\App\Http\Controllers\AircraftController::class, 'aircraftList']);
        Route::post('aircraft-details', [\App\Http\Controllers\AircraftController::class, 'aircraftDetails']);
        Route::post('aircraft-filter', [\App\Http\Controllers\AircraftController::class, 'aircraftFilter']);

        // Payment Routes
        Route::post('payment-details', [\App\Http\Controllers\PaymentController::class, 'paymentDetails']);
        Route::post('payment-create', [\App\Http\Controllers\PaymentController::class, 'createPayment']);
        Route::post('payment-edit', [\App\Http\Controllers\PaymentController::class, 'editPayment']);
        Route::post('payment-delete', [\App\Http\Controllers\PaymentController::class, 'deletePayment']);
    });
});