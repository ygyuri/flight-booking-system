<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class AirportController extends Controller
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
            'createAirport', 'editAirport', 'deleteAirport'
        ]);
    }

    // Routes accessible by both admins and customers
    /**
     * Get list of airports.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function airportList(Request $request)
    {
        // Fetch airports with relationships and counts
        $airports = Airport::withCount('flights', 'arrivalFlights')->latest();

        // Check if pagination is requested
        if ($request->query('paginate') == 'true') {
            $airports = $airports->paginate();
        } else {
            $airports = $airports->get();
        }

        // Return JSON response
        return response()->json($airports);
    }


    /**
     * Get details of a specific airport.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function airportDetails(Request $request)
    {
        // Validate request
        $request->validate(['id' => 'required']);

        // Find airport by ID with relationships
        $airport = Airport::with('flights', 'arrivalFlights')->find($request->id);

        // Check if airport exists
        if (!$airport) {
            return response()->json([
                'status' => 'error',
                'message' => 'Airport not found'
            ], 404);
        }

        // Return airport details as JSON response
        return response()->json($airport);
    }


    /**
     * Filter airports based on various criteria.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function airportFilter(Request $request)
    {
        // Retrieve request parameters
        $city = $request->city;
        $country = $request->country;
        $timezone = $request->timezone;
        $elevation = $request->elevation;
        // Add any additional parameters as needed

        // Start building the query to retrieve airports
        $airports = Airport::query();

        // Apply filters based on request parameters
        if ($city) {
            $airports->where('city', $city);
        }

        if ($country) {
            $airports->where('country', $country);
        }

        if ($timezone) {
            $airports->where('timezone', $timezone);
        }

        if ($elevation) {
            $airports->where('elevation', $elevation);
        }

        // Retrieve paginated results
        $airports = $airports->paginate();

        // Return paginated airports as JSON response
        return response()->json($airports);
    }


    // Routes accessible by admin only

    /**
     * Create a new airport.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAirport(Request $request)
    {
        // Validate request
        $request->validate([
            'name' => 'required',
            'city' => 'required',
            'country' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'timezone' => 'required',
            'elevation' => 'required',
            // Add any additional validation rules here
        ]);

        // Start database transaction
        DB::beginTransaction();
        try {
            // Create airport with individual fields
            $airport = Airport::create([
                'name' => $request->name,
                'city' => $request->city,
                'country' => $request->country,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'timezone' => $request->timezone,
                'elevation' => $request->elevation,
                // Add any additional fields here
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
            'message' => 'Airport created successfully'
        ], 201);
    }

    /**
     * Edit an existing airport.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editAirport(Request $request)
    {
        // Validate request
        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'city' => 'required',
            'country' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'timezone' => 'required',
            'elevation' => 'required',
            // Add any additional validation rules here
        ]);

        // Find airport by ID
        $airport = Airport::find($request->id);

        // Check if airport exists
        if (!$airport) {
            return response()->json([
                'status' => 'error',
                'message' => 'Airport not found'
            ], 404);
        }

        // Start database transaction
        DB::beginTransaction();
        try {
            // Update airport attributes
            $airport->update([
                'name' => $request->name,
                'city' => $request->city,
                'country' => $request->country,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'timezone' => $request->timezone,
                'elevation' => $request->elevation,
                // Add any additional fields here
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
            'message' => 'Airport updated successfully'
        ]);
    }

    /**
     * Delete an airport.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAirport(Request $request)
    {
        // Validate request to ensure the presence of airport ID
        $request->validate(['id' => 'required']);

        // Find airport by ID
        $airport = Airport::find($request->id);

        // Check if airport exists
        if (!$airport) {
            // Return error response if airport not found
            return response()->json([
                'status' => 'error',
                'message' => 'Airport not found'
            ], 404);
        }

        // Attempt to delete airport
        try {
            // Start a database transaction
            DB::beginTransaction();

            // Delete the airport
            $airport->delete();

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
            'message' => 'Airport deleted successfully'
        ]);
    }
}