<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountManagementController extends Controller
{
    /**
     * Verify that the currently logged-in user is a Super Admin
     * and confirm their current password before performing sensitive actions.
     */
    private function authorizeSuperAdminWithPassword(string $confirmPassword)
    {
        if (! session('is_admin') || ! session('admin_user_id')) {
            abort(403, 'Unauthorized session.');
        }

        $superAdmin = DB::table('system_user')
            ->where('User_ID', session('admin_user_id'))
            ->first();

        $allowedRoles = ['super admin', 'superadmin', 'punong barangay'];

        if (! $superAdmin || ! in_array(strtolower(trim($superAdmin->Role ?? '')), $allowedRoles, true)) {
            abort(403, 'Access denied. Super Admin privileges required.');
        }

        if (! Hash::check($confirmPassword, $superAdmin->Password)) {
            return null; // Password verification failed
        }

        return $superAdmin;
    }

    /**
     * Create a new barangay official account.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'              => ['required', 'string', 'max:255'],
            'username'               => ['required', 'string', 'max:100', 'unique:system_user,Username'],
            'role'                   => ['required', 'string', 'max:100'],
            'password'               => ['required', 'string', 'min:8', 'confirmed'],
            'super_admin_password'   => ['required', 'string'],
        ]);

        if (! $this->authorizeSuperAdminWithPassword($validated['super_admin_password'])) {
            return redirect()->route('admin.dashboard', ['tab' => 'accounts'])
                ->withErrors(['super_admin_password' => 'Verification failed: Incorrect Super Admin password.'])
                ->withInput($request->except(['password', 'password_confirmation', 'super_admin_password']));
        }

        DB::table('system_user')->insert([
            'Full_Name' => trim($validated['full_name']),
            'Username'  => trim($validated['username']),
            'Role'      => trim($validated['role']),
            'Password'  => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.dashboard', ['tab' => 'accounts'])
            ->with('status', 'Barangay official account created successfully for ' . $validated['full_name'] . '.');
    }

    /**
     * Change the password of an existing official account after re-verifying Super Admin password.
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'target_user_id'            => ['required', 'integer', 'exists:system_user,User_ID'],
            'new_password'              => ['required', 'string', 'min:8', 'confirmed'],
            'super_admin_password'      => ['required', 'string'],
        ]);

        if (! $this->authorizeSuperAdminWithPassword($validated['super_admin_password'])) {
            return redirect()->route('admin.dashboard', ['tab' => 'accounts'])
                ->withErrors(['super_admin_password' => 'Verification failed: The Super Admin password you entered is incorrect.']);
        }

        $targetUser = DB::table('system_user')
            ->where('User_ID', $validated['target_user_id'])
            ->first();

        DB::table('system_user')
            ->where('User_ID', $validated['target_user_id'])
            ->update([
                'Password' => Hash::make($validated['new_password']),
            ]);

        return redirect()->route('admin.dashboard', ['tab' => 'accounts'])
            ->with('status', 'Password updated successfully for ' . ($targetUser->Full_Name ?? $targetUser->Username) . '.');
    }
}
