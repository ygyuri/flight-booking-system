<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;
use Carbon\Carbon;

class BookingController extends Controller
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
     * Get list of bookings.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bookingList(Request $request)
    {
        // Fetch bookings with relationships
        $bookings = Booking::with([
            'customer',    // Relationship with customer
            'flight',      // Relationship with flight
            'seat',        // Relationship with seat
        ])->latest();

        // Check if pagination is requested
        if ($request->query('paginate') == 'true') {
            $bookings = $bookings->paginate();
        } else {
            $bookings = $bookings->get();
        }

        // Return JSON response
        return response()->json($bookings);
    }

    /**
     * Get details of a specific booking.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bookingDetails(Request $request)
    {
        // Validate request
        $request->validate(['id' => 'required']);

        // Find booking by ID with relationships
        $booking = Booking::with([
            'customer',    // Relationship with customer
            'flight',      // Relationship with flight
            'seat',        // Relationship with seat
        ])->find($request->id);

        // Check if booking exists
        if (!$booking) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking not found'
            ], 404);
        }

        // Return JSON response
        return response()->json($booking);
    }

    /**
     * Create a new booking.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createBooking(Request $request)
    {
        // Validate request
        $request->validate([
            'customer_id' => 'required',
            'flight_id' => 'required',
            'passenger_count' => 'required',
            'seat_id' => 'required',
            'total_price' => 'required',
            'status' => 'required',
            'payment_status' => 'required',
            'booking_date' => 'required|date',
            'booking_reference' => 'required',
            // Add any additional validation rules here
        ]);

        // Start database transaction
        DB::beginTransaction();
        try {
            // Create booking
            $booking = Booking::create([
                // Fill in booking attributes based on request data
                'customer_id' => $request->customer_id,
                'flight_id' => $request->flight_id,
                'passenger_count' => $request->passenger_count,
                'seat_id' => $request->seat_id,
                'total_price' => $request->total_price,
                'status' => $request->status,
                'payment_status' => $request->payment_status,
                'booking_date' => $request->booking_date,
                'booking_reference' => $request->booking_reference,
                'notes' => $request->notes,
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
            'message' => 'Booking created successfully'
        ], 201);
    }

    /**
     * Edit an existing booking.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editBooking(Request $request)
    {
        // Validate request
        $request->validate([
            'id' => 'required',
            'customer_id' => 'required',
            'flight_id' => 'required',
            'passenger_count' => 'required',
            'seat_id' => 'required',
            'total_price' => 'required',
            'status' => 'required',
            'payment_status' => 'required',
            'booking_date' => 'required|date',
            'booking_reference' => 'required',
            // Add any additional validation rules here
        ]);

        // Find booking by ID
        $booking = Booking::find($request->id);

        // Check if booking exists
        if (!$booking) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking not found'
            ], 404);
        }

        // Start database transaction
        DB::beginTransaction();
        try {
            // Update booking attributes
            $booking->update([
                'customer_id' => $request->customer_id,
                'flight_id' => $request->flight_id,
                'passenger_count' => $request->passenger_count,
                'seat_id' => $request->seat_id,
                'total_price' => $request->total_price,
                'status' => $request->status,
                'payment_status' => $request->payment_status,
                'booking_date' => $request->booking_date,
                'booking_reference' => $request->booking_reference,
                'notes' => $request->notes,
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
            'message' => 'Booking updated successfully'
        ]);
    }

    /**
     * Delete a booking.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteBooking(Request $request)
    {
        // Validate request to ensure the presence of booking ID
        $request->validate(['id' => 'required']);

        // Find booking by ID
        $booking = Booking::find($request->id);

        // Check if booking exists
        if (!$booking) {
            // Return error response if booking not found
            return response()->json([
                'status' => 'error',
                'message' => 'Booking not found'
            ], 404);
        }

        // Attempt to delete booking
        try {
            // Start a database transaction
                        DB::beginTransaction();

            // Delete the booking
            $booking->delete();

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
            'message' => 'Booking deleted successfully'
        ]);
    }

    /**
     * Filter bookings based on various criteria.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bookingFilter(Request $request)
    {
        // Retrieve request parameters
        $customer_id = $request->customer_id;
        $flight_id = $request->flight_id;
        $passenger_count = $request->passenger_count;
        $seat_id = $request->seat_id;
        $total_price = $request->total_price;
        $status = $request->status;
        $payment_status = $request->payment_status;
        $booking_date = $request->booking_date;
        $booking_reference = $request->booking_reference;

        // Start building the query to retrieve bookings
        $bookings = Booking::query();

        // Apply filters based on request parameters
        if ($customer_id) {
            $bookings->where('customer_id', $customer_id);
        }

        if ($flight_id) {
            $bookings->where('flight_id', $flight_id);
        }

        if ($passenger_count) {
            $bookings->where('passenger_count', $passenger_count);
        }

        if ($seat_id) {
            $bookings->where('seat_id', $seat_id);
        }

        if ($total_price) {
            $bookings->where('total_price', $total_price);
        }

        if ($status) {
            $bookings->where('status', $status);
        }

        if ($payment_status) {
            $bookings->where('payment_status', $payment_status);
        }

        if ($booking_date) {
            $bookings->whereDate('booking_date', Carbon::parse($booking_date)->toDateString());
        }

        if ($booking_reference) {
            $bookings->where('booking_reference', $booking_reference);
        }

        // Retrieve paginated results
        $bookings = $bookings->paginate();

        // Return paginated bookings as JSON response
        return response()->json($bookings);
    }
}