<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Apply 'admin' middleware to all methods
        $this->middleware('admin');
    }
    
    /**
     * Display a listing of admins.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function adminList(Request $request)
    {
        // Retrieve all admins with relationships and counts
        $admins = Admin::withCount('relations')->latest();

        // Check if pagination is requested
        if ($request->query('paginate') == 'true') {
            // Paginate the results
            $admins = $admins->paginate();
        } else {
            // Get all results
            $admins = $admins->get();
        }

        // Return JSON response with admins data
        return response()->json($admins);
    }

    /**
     * Display the specified admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function adminDetails(Request $request)
    {
        // Validate request parameters
        $request->validate(['id' => 'required']);

        // Find the admin by id
        $admin = Admin::find($request->id);

        // If admin not found, return error response
        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Admin not found'
            ], 404);
        }

        // Return JSON response with admin data
        return response()->json($admin);
    }

    /**
     * Create a new admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createAdmin(Request $request)
    {
        // Validate request parameters
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|min:8'
        ]);

        // Begin database transaction
        DB::beginTransaction();

        try {
            // Create new admin record
            $admin = Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);

            // Commit the transaction
            DB::commit();

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Admin created successfully'
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
     * Update the specified admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function editAdmin(Request $request)
    {
        // Validate request parameters
        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'email' => 'required|email|unique:admins,email,' . $request->id,
            'password' => 'nullable|min:8'
        ]);

        // Begin database transaction
        DB::beginTransaction();

        try {
            // Find the admin by id
            $admin = Admin::find($request->id);

            // If admin not found, return error response
            if (!$admin) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Admin not found'
                ], 404);
            }

            // Update admin data
            $admin->name = $request->name;
            $admin->email = $request->email;
            if ($request->has('password')) {
                $admin->password = bcrypt($request->password);
            }
            $admin->save();

            // Commit the transaction
            DB::commit();

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Admin updated successfully'
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
     * Remove the specified admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteAdmin(Request $request)
    {
        // Validate request parameters
        $request->validate(['id' => 'required']);

        // Find the admin by id
        $admin = Admin::find($request->id);

        // If admin not found, return error response
        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Admin not found'
            ], 404);
        }

        try {
            // Delete the admin
            $admin->delete();
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
            'message' => 'Admin deleted successfully'
        ]);
    }

    /**
     * Filter admins based on given criteria.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function adminFilter(Request $request)
    {
        // Retrieve all request parameters
        $name = $request->name;
        $email = $request->email;
        // You can add more filter parameters as per your requirements

        // Query to retrieve admins based on filter criteria
        $admins = Admin::when($name, function ($query) use ($name) {
            return $query->where('name', 'like', "%$name%");
        })
        ->when($email, function ($query) use ($email) {
            return $query->where('email', 'like', "%$email%");
        })
        // Add more filters here as per your requirements
        ->get();

        // Return JSON response with filtered admins
        return response()->json($admins);
    }
}