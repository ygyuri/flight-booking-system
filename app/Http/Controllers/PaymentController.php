<?php
namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class PaymentController extends Controller
{
    
    public function __construct()
    {
        // Apply 'adminOrCustomer' middleware to all methods
        $this->middleware('adminOrCustomer')->only([
            'createPayment', 'editPayment', 'deletePayment', 'paymentDetails'
        ]);

        // Apply 'admin' middleware to methods requiring admin authentication
        $this->middleware('admin')->only([
            'paymentList', 'paymentFilter'
        ]);
    }


    /**
     * Create a new payment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createPayment(Request $request)
    {
        // Validation rules
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required',
            'status' => 'required',
            'transaction_id' => 'required|unique:payments,transaction_id',
            'payment_date' => 'required|date',
        ]);

        // Begin database transaction
        DB::beginTransaction();
        try {
            // Create new payment
            $payment = Payment::create([
                'booking_id' => $request->booking_id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'status' => $request->status,
                'transaction_id' => $request->transaction_id,
                'payment_date' => $request->payment_date,
            ]);

            // Commit transaction
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment created successfully',
                'payment' => $payment
            ], 201);
        } catch (Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Edit an existing payment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editPayment(Request $request)
    {
        // Validation rules
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required',
            'status' => 'required',
            'transaction_id' => [
                'required',
                Rule::unique('payments')->ignore($request->payment_id),
            ],
            'payment_date' => 'required|date',
        ]);

        // Begin database transaction
        DB::beginTransaction();
        try {
            // Find payment by ID
            $payment = Payment::findOrFail($request->payment_id);

            // Update payment
            $payment->booking_id = $request->booking_id;
            $payment->amount = $request->amount;
            $payment->payment_method = $request->payment_method;
            $payment->status = $request->status;
            $payment->transaction_id = $request->transaction_id;
            $payment->payment_date = $request->payment_date;
            $payment->save();

            // Commit transaction
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment updated successfully',
                'payment' => $payment
            ]);
        } catch (Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Delete a payment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePayment(Request $request)
    {
        // Validation rules
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
        ]);

        try {
            // Find payment by ID and delete
            Payment::findOrFail($request->payment_id)->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

     /**
     * Get details of a payment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function paymentDetails(Request $request)
    {
        // Validation rules
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
        ]);

        try {
            // Find payment by ID with eager loading of the booking relationship
            $payment = Payment::with('booking')->findOrFail($request->payment_id);

            return response()->json([
                'status' => 'success',
                'payment' => $payment
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a list of payments.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function paymentList(Request $request)
    {
        try {
            // Fetch list of payments with eager loading of the booking relationship
            $payments = Payment::with('booking')->latest()->paginate();

            return response()->json([
                'status' => 'success',
                'payments' => $payments
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

     /**
     * Filter payments based on criteria.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function paymentFilter(Request $request)
    {
        // Validation rules for filtering criteria
        $request->validate([
            'type' => 'nullable|in:internal,citizen',
            'booking_id' => 'nullable|exists:bookings,id',
            'status' => 'nullable',
            'payment_date' => 'nullable|date',
            'custom_date' => 'nullable|date',
            'custom_start_date' => 'nullable|date',
            'custom_end_date' => 'nullable|date|after_or_equal:custom_start_date',
        ]);

        try {
            // Build the query based on filtering criteria
            $filteredPayments = Payment::latest();

            if ($request->type) {
                $filteredPayments->where('type', $request->type);
            }

            if ($request->booking_id) {
                $filteredPayments->where('booking_id', $request->booking_id);
            }

            if ($request->status) {
                $filteredPayments->where('status', $request->status);
            }

            if ($request->payment_date) {
                $filteredPayments->whereDate('payment_date', $request->payment_date);
            }

            if ($request->custom_date) {
                $filteredPayments->whereDate('payment_date', $request->custom_date);
            }

            if ($request->custom_start_date && $request->custom_end_date) {
                $filteredPayments->whereBetween('payment_date', [
                    $request->custom_start_date,
                    $request->custom_end_date
                ]);
            }

            // Execute the query
            $filteredPayments = $filteredPayments->get();

            return response()->json([
                'status' => 'success',
                'filteredPayments' => $filteredPayments
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


}
