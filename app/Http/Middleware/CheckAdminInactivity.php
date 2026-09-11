<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminInactivity
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session('is_admin')) {
            $lastActivity = session('admin_last_activity');
            $timeout = 120; // 2 minutes in seconds

            if ($lastActivity && (time() - $lastActivity > $timeout)) {
                // Wipe the admin session keys
                $request->session()->forget([
                    'is_admin',
                    'admin_user_id',
                    'admin_username',
                    'admin_last_activity'
                ]);

                return redirect()->route('admin.login')->with('status', 'You have been logged out due to 2 minutes of inactivity.');
            }

            // Update timestamp on active requests
            session(['admin_last_activity' => time()]);
        }

        return $next($request);
    }
}