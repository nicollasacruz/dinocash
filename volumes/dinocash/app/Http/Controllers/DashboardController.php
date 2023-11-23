<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $activeSessions = DB::table('sessions')
            ->where('last_activity', '>', now()->subMinutes(config('session.lifetime')))
            ->count();
        $totalUsers = User::all()->count();
        $lastUsers = User::latest('created_at')->limit(50)->get();

        return Inertia::render('Dashboard', [
            'activeSessions' => $activeSessions,
            'totalUsers' => $totalUsers,
            'lastUsers' => $lastUsers,
        ]);
    }
}
