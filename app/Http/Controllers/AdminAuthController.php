<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function loginForm(Request $request)
    {
        if (session('is_admin')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($credentials['username'] === 'admin' && $credentials['password'] === 'admin') {
            $request->session()->regenerate();
            $request->session()->put('is_admin', true);

            return redirect()->route('admin.dashboard');
        }

        return back()
            ->withErrors(['login' => 'Invalid credentials. Use admin / admin for this prototype.'])
            ->onlyInput('username');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('is_admin');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
