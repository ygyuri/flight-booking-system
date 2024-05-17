<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
  public function handle(Request $request, Closure $next)
  {
    if (Auth::guard('admin')->check()) {
      return $next($request);
    }

    // Redirect with error message (flash session)
    return redirect('/login')->withErrors([
      'message' => 'You are not authorized to access this resource as an Admin, Please log in.'
    ]);
  }
}