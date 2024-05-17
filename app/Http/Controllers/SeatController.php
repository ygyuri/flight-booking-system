<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class SeatController extends Controller
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
            'createSeat', 'editSeat', 'deleteSeat'
        ]);
    }

    /**
     * Create a new seat.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createSeat(Request $request)
    {
        // Validate request
        $request->validate([
            'seat_number' => 'required',
            'seat_class' => 'required',
            'availability_status' => 'required',
            'price' => 'required',
            'flight_id' => 'required',
            // Add any additional validation rules here
        ]);

        // Start database transaction
        DB::beginTransaction();
        try {
            // Create seat
            $seat = Seat::create([
                // Fill in seat attributes based on request data
                'seat_number' => $request->seat_number,
                'seat_class' => $request->seat_class,
                'availability_status' => $request->availability_status,
                'price' => $request->price,
                'flight_id' => $request->flight_id,
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
            'message' => 'Seat created successfully'
        ], 201);
    }

    /**
     * Edit an existing seat.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editSeat(Request $request)
    {
        // Validate request
        $request->validate([
            'id' => 'required',
            'seat_number' => 'required',
            'seat_class' => 'required',
            'availability_status' => 'required',
            'price' => 'required',
            'flight_id' => 'required',
            // Add any additional validation rules here
        ]);

        // Find seat by ID
        $seat = Seat::find($request->id);

        // Check if seat exists
        if (!$seat) {
            return response()->json([
                'status' => 'error',
                'message' => 'Seat not found'
            ], 404);
        }

        // Start database transaction
        DB::beginTransaction();
        try {
            // Update seat attributes
            $seat->update([
                'seat_number' => $request->seat_number,
                'seat_class' => $request->seat_class,
                'availability_status' => $request->availability_status,
                'price' => $request->price,
                'flight_id' => $request->flight_id,
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
            'message' => 'Seat updated successfully'
        ]);
    }

    /**
     * Delete a seat.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteSeat(Request $request)
    {
        // Validate request to ensure the presence of seat ID
        $request->validate(['id' => 'required']);

        // Find seat by ID
        $seat = Seat::find($request->id);

        // Check if seat exists
        if (!$seat) {
            // Return error response if seat not found
            return response()->json([
                'status' => 'error',
                'message' => 'Seat not found'
            ], 404);
        }

        // Attempt to delete seat
        try {
            // Start a database transaction
            DB::beginTransaction();

            // Delete the seat
            $seat->delete();

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
            'message' => 'Seat deleted successfully'
        ]);
    }

    /**
     * Get list of seats.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function seatList(Request $request)
    {
        // Fetch seats with relationships
        $seats = Seat::with('flight', 'booking')->latest();

        // Check if pagination is requested
        if ($request->query('paginate') == 'true') {
            $seats = $seats->paginate();
        } else {
            $seats = $seats->get();
        }

        // Return JSON response
        return response()->json($seats);
    }

    /**
     * Get details of a specific seat.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function seatDetails(Request $request)
    {
        // Validate request
        $request->validate(['id' => 'required']);

        // Find seat by ID with relationship
        $seat = Seat::with('flight', 'booking')->find($request->id);

        // Check if seat exists
        if (!$seat) {
            return response()->json([
                'status' => 'error',
                'message' => 'Seat not found'
            ], 404);
        }

        // Return JSON response
        return response()->json($seat);
    }

    /**
     * Filter seats based on various criteria.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function seatFilter(Request $request)
    {
        // Retrieve request parameters
        $seat_number = $request->seat_number;
        $seat_class = $request->seat_class;
        $availability_status = $request->availability_status;
        $price = $request->price;
        $flight_id = $request->flight_id;

                // Start building the query to retrieve seats
                $seats = Seat::query();

                // Apply filters based on request parameters
                if ($seat_number) {
                    $seats->where('seat_number', $seat_number);
                }

                if ($seat_class) {
                    $seats->where('seat_class', $seat_class);
                }

                if ($availability_status) {
                    $seats->where('availability_status', $availability_status);
                }

                if ($price) {
                    $seats->where('price', $price);
                }

                if ($flight_id) {
                    $seats->where('flight_id', $flight_id);
                }

                // Retrieve paginated results
                $seats = $seats->paginate();

                // Return paginated seats as JSON response
                return response()->json($seats);
            }



}