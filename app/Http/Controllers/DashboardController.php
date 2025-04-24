<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        return view('dashboard', compact('user'));
    }

    /**
     * Show user-specific dashboard stats or information.
     *
     * @return \Illuminate\View\View
     */
    public function userStats()
    {
        $user = Auth::user();

        return view('user.stats', compact('user'));
    }

    /**
     * Show system-wide dashboard stats (potentially for admins only).
     *
     * @return \Illuminate\View\View
     */
    public function systemStats()
    {
        // You might want to add authorization check here
        // $this->authorize('view-system-stats');

        // Fetch system-wide stats
        // $userCount = User::count();
        // $activeUsers = User::where('last_active_at', '>=', now()->subDays(7))->count();

        return view('admin.system-stats');
    }
}
