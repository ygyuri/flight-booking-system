<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\FlightSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class FlightScheduleController extends Controller
{
    /**
     * Constructor to apply authentication middleware.
     */
    public function __construct()
    {
        // Apply 'adminOrCustomer' middleware to all methods
        $this->middleware('adminOrCustomer');

        // Apply 'admin' middleware to methods requiring admin authentication
        $this->middleware('admin')->only([
            'createFlightSchedule', 'editFlightSchedule', 'deleteFlightSchedule'
        ]);
    }

    /**
     * Get list of flight schedules.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function flightScheduleList(Request $request)
    {
        // Fetch flight schedules with relationships
        $flightSchedules = FlightSchedule::with('flight')->latest();

        // Check if pagination is requested
        if ($request->query('paginate') == 'true') {
            $flightSchedules = $flightSchedules->paginate();
        } else {
            $flightSchedules = $flightSchedules->get();
        }

        // Return JSON response
        return response()->json($flightSchedules);
    }


    /**
     * Get details of a specific flight schedule.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function flightScheduleDetails(Request $request)
    {
        // Validate request
        $request->validate(['id' => 'required']);

        // Find flight schedule by ID with relationship
        $flightSchedule = FlightSchedule::with('flight')->find($request->id);

        // Check if flight schedule exists
        if (!$flightSchedule) {
            return response()->json([
                'status' => 'error',
                'message' => 'Flight schedule not found'
            ], 404);
        }

        // Return JSON response
        return response()->json($flightSchedule);
    }

    /**
     * Create a new flight schedule.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createFlightSchedule(Request $request)
    {
        // Validate request
        $request->validate([
            'flight_id' => 'required',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date|after:departure_time',
            'status' => ['required', Rule::in(['scheduled', 'delayed', 'canceled'])],
        ]);

        // Start database transaction
        DB::beginTransaction();
        try {
            // Create flight schedule
            $flightSchedule = FlightSchedule::create([
                'flight_id' => $request->flight_id,
                'departure_time' => $request->departure_time,
                'arrival_time' => $request->arrival_time,
                'status' => $request->status,
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
            'message' => 'Flight schedule created successfully'
        ], 201);
    }

    /**
     * Edit an existing flight schedule.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editFlightSchedule(Request $request)
    {
        // Validate request
        $request->validate([
            'id' => 'required',
            'flight_id' => 'required',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date|after:departure_time',
            'status' => ['required', Rule::in(['scheduled', 'delayed', 'canceled'])],
        ]);

        // Find flight schedule by ID
        $flightSchedule = FlightSchedule::find($request->id);

        // Check if flight schedule exists
        if (!$flightSchedule) {
            return response()->json([
                'status' => 'error',
                'message' => 'Flight schedule not found'
            ], 404);
        }

        // Start database transaction
        DB::beginTransaction();
        try {
            // Update flight schedule attributes
            $flightSchedule->update([
                'flight_id' => $request->flight_id,
                'departure_time' => $request->departure_time,
                'arrival_time' => $request->arrival_time,
                'status' => $request->status,
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
            'message' => 'Flight schedule updated successfully'
        ]);
    }

    /**
     * Delete a flight schedule.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteFlightSchedule(Request $request)
    {
        // Validate request
        $request->validate(['id' => 'required']);

        // Find flight schedule by ID
        $flightSchedule = FlightSchedule::find($request->id);

        // Check if flight schedule exists
        if (!$flightSchedule) {
            // Return error response if flight schedule not found
            return response()->json([
                'status' => 'error',
                'message' => 'Flight schedule not found'
            ], 404);
        }

        // Attempt to delete flight schedule
        try {
            // Delete the flight schedule
            $flightSchedule->delete();
        } catch (Exception $e) {
            // Return error response on exception
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }

        // Return success response
        return response()->json([
            'status' => 'success',
            'message' => 'Flight schedule deleted successfully'
        ]);
    }

    /**
     * Filter flight schedules based on various criteria.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function flightScheduleFilter(Request $request)
    {
        // Retrieve request parameters
        $flight_id = $request->flight_id;
        $departure_time = $request->departure_time;
        $arrival_time = $request->arrival_time;
        $status = $request->status;

        // Start building the query to retrieve flight schedules
        $flightSchedules = FlightSchedule::query();

        // Apply filters based on request parameters
        if ($flight_id) {
            $flightSchedules->where('flight_id', $flight_id);
        }

        if ($departure_time) {
            $flightSchedules->where('departure_time', $departure_time);
        }

        if ($arrival_time) {
            $flightSchedules->where('arrival_time', $arrival_time);
        }

        if ($status)
        {
            $flightSchedules->where('status', $status);
        }

        // Retrieve paginated results
        $flightSchedules = $flightSchedules->paginate();

        // Return paginated flight schedules as JSON response
        return response()->json($flightSchedules);
    }
}