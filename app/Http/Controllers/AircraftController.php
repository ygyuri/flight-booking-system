<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Aircraft;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class AircraftController extends Controller
{
    /**
     * Constructor to apply middleware.
     */
    public function __construct()
    {
        // Apply 'adminOrCustomer' middleware to all methods
        $this->middleware('adminOrCustomer');

        // Apply 'admin' middleware to methods requiring admin authentication
        $this->middleware('admin')->only([
            'createAircraft', 'editAircraft', 'deleteAircraft'
        ]);
    }

    /**
     * Fetches a list of aircraft.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function aircraftList(Request $request)
    {
        // Retrieving aircraft with eager loaded relationships and counts
        $aircraft = Aircraft::with('flights')->withCount('flights')->latest();

        // Pagination check
        if ($request->query('paginate') == 'true') {
            $aircraft = $aircraft->paginate();
        } else {
            $aircraft = $aircraft->get();
        }

        return response()->json($aircraft);
    }

    /**
     * Fetches details of a specific aircraft.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function aircraftDetails(Request $request)
    {
        $request->validate(['id' => 'required']);

        // Finding aircraft with eager loaded relationships and view count
        $aircraft = Aircraft::with('flights')->withCount('flights')->find($request->id);

        if (!$aircraft) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aircraft not found'
            ], 404);
        }

        return response()->json($aircraft);
    }
    /**
     * Creates a new aircraft.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAircraft(Request $request)
    {
        // Validating incoming request fields
        $request->validate([
            'name' => 'required',
            'manufacturer' => 'required',
            'registration_number' => 'required|unique:aircrafts',
            'sitting_capacity' => 'required|integer|min:1',
        ]);

        // Create aircraft
        try {
            $aircraft = Aircraft::create([
                'name' => $request->name,
                'manufacturer' => $request->manufacturer,
                'registration_number' => $request->registration_number,
                'sitting_capacity' => $request->sitting_capacity,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Aircraft created successfully'
        ], 201);
    }

    /**
     * Edits an existing aircraft.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editAircraft(Request $request)
    {
        // Validating incoming request fields
        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'manufacturer' => 'required',
            'registration_number' => 'required|unique:aircrafts,registration_number,' . $request->id,
            'sitting_capacity' => 'required|integer|min:1',
        ]);

        $aircraft = Aircraft::find($request->id);

        if (!$aircraft) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aircraft not found'
            ], 404);
        }

        // Update aircraft
        try {
            $aircraft->update([
                'name' => $request->name,
                'manufacturer' => $request->manufacturer,
                'registration_number' => $request->registration_number,
                'sitting_capacity' => $request->sitting_capacity,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Aircraft updated successfully'
        ]);
    }

    /**
     * Deletes an existing aircraft.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAircraft(Request $request)
    {
        $request->validate(['id' => 'required']);

        $aircraft = Aircraft::find($request->id);

        if (!$aircraft) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aircraft not found'
            ], 404);
        }

        // Delete aircraft
        try {
            $aircraft->delete();
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Aircraft deleted successfully'
        ]);
    }

    /**
     * Filters aircraft based on specified criteria.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function aircraftFilter(Request $request)
    {
        // Retrieving filters from request
        $name = $request->name;
        $manufacturer = $request->manufacturer;
        $registration_number = $request->registration_number;
        $sitting_capacity = $request->sitting_capacity;

        // Query aircraft with eager loaded relationships and counts
        $aircraft = Aircraft::with('flights')->withCount('flights')->latest();

        // Applying filters
        if ($name) {
            $aircraft->where('name', 'like', '%' . $name . '%');
        }

        if ($manufacturer) {
            $aircraft->where('manufacturer', 'like', '%' . $manufacturer . '%');
        }

        if ($registration_number) {
            $aircraft->where('registration_number', 'like', '%' . $registration_number . '%');
        }

        if ($sitting_capacity) {
            $aircraft->where('sitting_capacity', $sitting_capacity);
        }

        // Paginating results
        $aircraft = $aircraft->paginate();

        return response()->json($aircraft);
    }
}