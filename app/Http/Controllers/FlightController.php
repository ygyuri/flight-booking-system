<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Flight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

use Carbon\Carbon;



class FlightController extends Controller
{
    /**
     * Constructor to apply authentication middleware.
     */
    public function __construct()
    {
    // Apply 'adminOrCustomer' middleware to all methods
    $this->middleware('adminOrCustomer');
    }



    /**
     * Get list of flights.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function flightList(Request $request)
    {
        // Fetch flights with relationships
        $flights = Flight::with([
            'departureAirport',    // Relationship with departure airport
            'arrivalAirport',      // Relationship with arrival airport
            'aircraft',            // Relationship with aircraft
            'bookings',            // Relationship with bookings
            'seats',               // Relationship with seats
            'schedule',            // Relationship with schedule
            'payments',            // Relationship with payments
        ])->latest();

        // Check if pagination is requested
        if ($request->query('paginate') == 'true') {
            $flights = $flights->paginate();
        } else {
            $flights = $flights->get();
        }

        // Return JSON response
        return response()->json($flights);
    }


        /**
     * Get details of a specific flight.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function flightDetails(Request $request)
    {
        // Validate request
        $request->validate(['id' => 'required']);

        // Find flight by ID with relationships
        $flight = Flight::with([
            'departureAirport',    // Relationship with departure airport
            'arrivalAirport',      // Relationship with arrival airport
            'aircraft',            // Relationship with aircraft
            'bookings',            // Relationship with bookings
            'seats',               // Relationship with seats
            'schedule',            // Relationship with schedule
            'payments',            // Relationship with payments
        ])->find($request->id);

        // Check if flight exists
        if (!$flight) {
            return response()->json([
                'status' => 'error',
                'message' => 'Flight not found'
            ], 404);
        }

        // Return JSON response
        return response()->json($flight);
    }


        /**
     * Create a new flight.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createFlight(Request $request)
    {
        // Validate request
        $request->validate([
            'flight_number' => 'required',
            'departure_airport_id' => 'required',
            'arrival_airport_id' => 'required',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date',
            'duration' => 'required',
            'aircraft_id' => 'required',
            'price' => 'required',
            'status' => 'required',
            'available_seats' => 'required',
            // Add any additional validation rules here
        ]);

        // Start database transaction
        DB::beginTransaction();
        try {
            // Create flight
            $flight = Flight::create([
                // Fill in flight attributes based on request data
                'flight_number' => $request->flight_number,
                'departure_airport_id' => $request->departure_airport_id,
                'arrival_airport_id' => $request->arrival_airport_id,
                'departure_time' => $request->departure_time,
                'arrival_time' => $request->arrival_time,
                'duration' => $request->duration,
                'aircraft_id' => $request->aircraft_id,
                'price' => $request->price,
                'status' => $request->status,
                'available_seats' => $request->available_seats,
            ]);

            // Commit transaction
            DB::commit();
        } catch (Exception $e) {
            // Rollback transaction on exception
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }

        // Return success response
        return response()->json([
            'status' => 'success',
            'message' => 'Flight created successfully'
        ], 201);
    }



    /**
     * Edit an existing flight.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editFlight(Request $request)
    {
        // Validate request
        $request->validate([
            'id' => 'required',
            'flight_number' => 'required',
            'departure_airport_id' => 'required',
            'arrival_airport_id' => 'required',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date',
            'duration' => 'required',
            'aircraft_id' => 'required',
            'price' => 'required',
            'status' => 'required',
            'available_seats' => 'required',
            // Add any additional validation rules here
        ]);

        // Find flight by ID
        $flight = Flight::find($request->id);

        // Check if flight exists
        if (!$flight) {
            return response()->json([
                'status' => 'error',
                'message' => 'Flight not found'
            ], 404);
        }

        // Start database transaction
        DB::beginTransaction();
        try {
            // Update flight attributes
            $flight->update([
                'flight_number' => $request->flight_number,
                'departure_airport_id' => $request->departure_airport_id,
                'arrival_airport_id' => $request->arrival_airport_id,
                'departure_time' => $request->departure_time,
                'arrival_time' => $request->arrival_time,
                'duration' => $request->duration,
                'aircraft_id' => $request->aircraft_id,
                'price' => $request->price,
                'status' => $request->status,
                'available_seats' => $request->available_seats,
            ]);

            // Commit transaction
            DB::commit();
        } catch (Exception $e) {
            // Rollback transaction on exception
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }

        // Return success response
        return response()->json([
            'status' => 'success',
            'message' => 'Flight updated successfully'
        ]);
    }


    /**
     * Delete a flight.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteFlight(Request $request)
    {
        // Validate request to ensure the presence of flight ID
        $request->validate(['id' => 'required']);

        // Find flight by ID
        $flight = Flight::find($request->id);

        // Check if flight exists
        if (!$flight) {
            // Return error response if flight not found
            return response()->json([
                'status' => 'error',
                'message' => 'Flight not found'
            ], 404);
        }

        // Attempt to delete flight
        try {
            // Start a database transaction
            DB::beginTransaction();

            // Delete the flight
            $flight->delete();

            // Commit transaction
            DB::commit();
        } catch (Exception $e) {
            // Rollback transaction on exception and return error response
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }

        // Return success response
        return response()->json([
            'status' => 'success',
            'message' => 'Flight deleted successfully'
        ]);
    }



    /**
     * Filter flights based on various criteria.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function flightFilter(Request $request)
    {
        // Retrieve request parameters
        $departure_airport_id = $request->departure_airport_id;
        $arrival_airport_id = $request->arrival_airport_id;
        $departure_time = $request->departure_time;
        $arrival_time = $request->arrival_time;
        $duration = $request->duration;
        $aircraft_id = $request->aircraft_id;
        $status = $request->status;
        $price = $request->price;
        $available_seats = $request->available_seats;
        $created_date = $request->created_date;
        $custom_date = $request->custom_date;
        $custom_start_date = $request->custom_start_date;
        $custom_end_date = $request->custom_end_date;

        // Start building the query to retrieve flights
        $flights = Flight::query();

        // Apply filters based on request parameters
        if ($departure_airport_id) {
            $flights->where('departure_airport_id', $departure_airport_id);
        }

        if ($arrival_airport_id) {
            $flights->where('arrival_airport_id', $arrival_airport_id);
        }

        if ($departure_time) {
            $flights->whereDate('departure_time', Carbon::parse($departure_time)->toDateString());
        }

        if ($arrival_time) {
            $flights->whereDate('arrival_time', Carbon::parse($arrival_time)->toDateString());
        }

        if ($duration) {
            // Duration could be a range, but for simplicity, let's assume it's an exact match
            $flights->where('duration', $duration);
        }

        if ($aircraft_id) {
            $flights->where('aircraft_id', $aircraft_id);
        }

        if ($status) {
            $flights->where('status', $status);
        }

        if ($price) {
            $flights->where('price', $price);
        }

        if ($available_seats) {
            $flights->where('available_seats', $available_seats);
        }

        if ($created_date && $created_date != 'all') {
            // Apply date filters based on the selected option
            // You can extend this part for more date filtering options
            switch ($created_date) {
                case 'today':
                    $flights->whereDate('created_at', Carbon::today());
                    break;
                case 'yesterday':
                    $flights->whereDate('created_at', Carbon::yesterday());
                    break;
                // Add more cases as needed
                default:
                    // Handle custom date ranges if needed
                    break;
            }
        }

        // Retrieve paginated results
        $flights = $flights->paginate();

        // Return paginated flights as JSON response
        return response()->json($flights);
    }

}