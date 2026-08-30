<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function loginForm(Request $request)
    {
        if (session('is_admin')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function registerForm(Request $request)
    {
        if (session('is_admin')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9._-]+$/', 'unique:system_user,Username'],
            'full_name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        DB::table('system_user')->insert([
            'Username' => trim($data['username']),
            'Password_Hash' => Hash::make($data['password']),
            'Role' => 'admin',
            'Full_Name' => trim($data['full_name']),
            'Is_Active' => 1,
        ]);

        return redirect()->route('admin.login')
            ->with('status', 'Admin account created successfully. You can now log in.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $adminUser = DB::table('system_user')
            ->where('Username', trim($credentials['username']))
            ->where('Role', 'admin')
            ->where('Is_Active', 1)
            ->first();

        if ($adminUser && Hash::check($credentials['password'], $adminUser->Password_Hash)) {
            $request->session()->regenerate();
            $request->session()->put('is_admin', true);
            $request->session()->put('admin_user_id', (int) $adminUser->User_ID);

            return redirect()->route('admin.dashboard');
        }

        return back()
            ->withErrors(['login' => 'Invalid administrator credentials.'])
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
