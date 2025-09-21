<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Show the signup form
     */
    public function showSignup()
    {
        return view('auth.signup');
    }

    /**
     * Handle user signup
     */
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'is_active' => true,
        ]);

        Auth::login($user);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Account created successfully!',
                'redirect' => route('dashboard')
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Account created successfully!');
    }

    /**
     * Show the login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle user login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Login successful!',
                    'redirect' => route('dashboard')
                ]);
            }
            
            return redirect()->route('dashboard')->with('success', 'Login successful!');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Invalid credentials',
                'errors' => ['email' => 'Invalid credentials']
            ], 422);
        }

        return redirect()->back()
            ->withErrors(['email' => 'Invalid credentials'])
            ->withInput();
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'Logged out successfully!');
    }

    /**
     * Show dashboard
     */
    public function dashboard()
    {
        return view('dashboard', ['user' => Auth::user()]);
    }
}
