<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Apply 'customer' middleware to all methods
        $this->middleware('customer');
    }

    /**
     * Display a listing of customers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function customerList(Request $request)
    {
        // Retrieve all customers with relationships and counts
        $customers = Customer::withCount('relations')->latest();

        // Check if pagination is requested
        if ($request->query('paginate') == 'true') {
            // Paginate the results
            $customers = $customers->paginate();
        } else {
            // Get all results
            $customers = $customers->get();
        }

        // Return JSON response with customers data
        return response()->json($customers);
    }

    /**
     * Display the specified customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function customerDetails(Request $request)
    {
        // Validate request parameters
        $request->validate(['id' => 'required']);

        // Find the customer by id
        $customer = Customer::find($request->id);

        // If customer not found, return error response
        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer not found'
            ], 404);
        }

        // Return JSON response with customer data
        return response()->json($customer);
    }

    /**
     * Create a new customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createCustomer(Request $request)
    {
        // Validate request parameters
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|min:8'
        ]);

        // Begin database transaction
        DB::beginTransaction();

        try {
            // Create new customer record
            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);

            // Commit the transaction
            DB::commit();

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Customer created successfully'
            ], 201);

        } catch (Exception $e) {
            // Rollback transaction if an exception occurs
            DB::rollBack();

            // Return error response
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function editCustomer(Request $request)
    {
        // Validate request parameters
        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'email' => 'required|email|unique:customers,email,' . $request->id,
            'password' => 'nullable|min:8'
        ]);

        // Begin database transaction
        DB::beginTransaction();

        try {
            // Find the customer by id
            $customer = Customer::find($request->id);

            // If customer not found, return error response
            if (!$customer) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Customer not found'
                ], 404);
            }

            // Update customer data
            $customer->name = $request->name;
            $customer->email = $request->email;
            if ($request->has('password')) {
                $customer->password = bcrypt($request->password);
            }
            $customer->save();

            // Commit the transaction
            DB::commit();

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Customer updated successfully'
            ]);

        } catch (Exception $e) {
            // Rollback transaction if an exception occurs
            DB::rollBack();

            // Return error response
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteCustomer(Request $request)
    {
        // Validate request parameters
        $request->validate(['id' => 'required']);

        // Find the customer by id
        $customer = Customer::find($request->id);

        // If customer not found, return error response
        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer not found'
            ], 404);
        }

        try {
            // Delete the customer
            $customer->delete();
        } catch (Exception $e) {
            // Return error response if deletion fails
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }

        // Return success response
        return response()->json([
            'status' => 'success',
            'message' => 'Customer deleted successfully'
        ]);
    }

    /**
     * Filter customers based on given criteria.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function customerFilter(Request $request)
    {
        // Retrieve all request parameters
        $name = $request->name;
        $email = $request->email;
        // You can add more filter parameters as per your requirements

        // Query to retrieve customers based on filter criteria
        $customers = Customer::when($name, function ($query) use ($name) {
            return $query->where('name', 'like', "%$name%");
        })
        ->when($email, function ($query) use ($email) {
            return $query->where('email', 'like', "%$email%");
        })
        // Add more filters here as per your requirements
        ->get();

        // Return JSON response with filtered customers
        return response()->json($customers);
    }
}