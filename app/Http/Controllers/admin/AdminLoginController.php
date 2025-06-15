<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function index()
    {
        // Jika user sudah login dan adalah admin, redirect ke dashboard
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        // Cek apakah user dengan email tersebut adalah admin
        $user = User::where('email', $credentials['email'])->first();
        
        if (!$user || $user->role !== 'admin') {
            return back()->withErrors([
                'email' => 'Email atau password tidak valid.',
            ])->withInput($request->only('email'));
        }
    
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Redirect langsung ke dashboard admin
            return redirect()->route('admin.dashboard');
        }
    
        return back()->withErrors([
            'email' => 'Email atau password tidak valid.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }

    public function showSignupForm()
    {
        return view('admin.signup');
    }

    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Buat user admin baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin'
        ]);

        // Redirect ke halaman login dengan pesan sukses
        return redirect()->route('admin.login');
    }
}
