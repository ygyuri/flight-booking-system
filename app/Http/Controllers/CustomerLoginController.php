<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('customer.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('customer')->attempt($credentials)) {
            return redirect()->intended('/customer/dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}