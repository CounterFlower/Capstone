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
            'username'  => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9._-]+$/', 'unique:system_user,Username'],
            'full_name' => ['required', 'string', 'max:255'],
            'role'      => ['required', 'string', 'in:Super Admin,Punong Barangay,Barangay Secretary,BHW'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        DB::table('system_user')->insert([
            'Username'      => trim($data['username']),
            'Password_Hash' => Hash::make($data['password']),
            'Role'          => $data['role'],
            'Full_Name'     => trim($data['full_name']),
            'Is_Active'     => 1,
        ]);

        return redirect()->route('admin.login')
            ->with('status', 'Account created successfully (' . $data['role'] . '). You can now log in.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Allow all valid barangay official roles (plus legacy 'admin') to authenticate
        $allowedRoles = [
            'Super Admin',
            'Punong Barangay',
            'Barangay Secretary',
            'BHW',
            'admin',
        ];

        $adminUser = DB::table('system_user')
            ->where('Username', trim($credentials['username']))
            ->whereIn('Role', $allowedRoles)
            ->where('Is_Active', 1)
            ->first();

        if ($adminUser && Hash::check($credentials['password'], $adminUser->Password_Hash)) {
            $request->session()->regenerate();
            $request->session()->put('is_admin', true);
            $request->session()->put('admin_user_id', (int) $adminUser->User_ID);
            $request->session()->put('admin_username', $adminUser->Username);
            $request->session()->put('admin_full_name', $adminUser->Full_Name);
            $request->session()->put('admin_role', $adminUser->Role);
            $request->session()->put('admin_last_activity', time());

            return redirect()->route('admin.dashboard');
        }

        return back()
            ->withErrors(['login' => 'Invalid official credentials or inactive account.'])
            ->onlyInput('username');
    }

    public function logout(Request $request)
    {
        $request->session()->forget([
            'is_admin',
            'admin_user_id',
            'admin_username',
            'admin_full_name',
            'admin_role',
            'admin_last_activity',
        ]);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
