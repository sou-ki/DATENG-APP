<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // app/Http/Controllers/AuthController.php
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            
            // Redirect berdasarkan role
            return match($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'internal' => redirect()->route('internal.dashboard'),
                'security' => redirect()->route('security.dashboard'),
                default => redirect('/home')
            };
        }
    }
}
