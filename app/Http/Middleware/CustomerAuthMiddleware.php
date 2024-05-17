<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * This middleware ensures only authenticated customers can access specific routes.
 */
class CustomerAuthMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    // Check if the user is authenticated with the 'customer' guard
    if (Auth::guard('customer')->check()) {
      return $next($request);
    }

    // User is not authenticated, redirect to login with an error message
    return redirect('/login')->withErrors([
      'message' => 'Please log in to access this resource as a customer.'
    ]);
  }
}