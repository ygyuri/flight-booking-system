<?php

// app/Http/Middleware/AdminOrCustomer.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminOrCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Check if the user is authenticated as either admin or customer
        if (Auth::guard('admin')->check() || Auth::guard('customer')->check()) {
            return $next($request);
        }

        // If not authenticated, redirect to a login page or show an error
        return redirect('/login'); // Adjust the redirect path as necessary
    }
}