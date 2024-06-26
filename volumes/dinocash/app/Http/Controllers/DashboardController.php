<?php

namespace App\Http\Controllers;

use App\Models\GameHistory;
use App\Models\Setting;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        
        $lossesTotal = GameHistory::lossesTotal()->with([
            'user' => function ($query) {
                $query
                    ->where('isAffiliate', false)
                    ->where('role', 'user');
            }
        ])->sum('finalAmount');
        $winsToday = GameHistory::winsToday()->with([
            'user' => function ($query) {
                $query
                    ->where('isAffiliate', false)
                    ->where('role', 'user');
            }
        ])->sum('finalAmount');
        $winsLast30Days = GameHistory::winsLast30Days()->with([
            'user' => function ($query) {
                $query
                    ->where('isAffiliate', false)
                    ->where('role', 'user');
            }
        ])->sum('finalAmount');
        $winsTotal = GameHistory::winsTotal()->with([
            'user' => function ($query) {
                $query
                    ->where('isAffiliate', false)
                    ->where('role', 'user');
            }
        ])->sum('finalAmount');
        $lossesToday = GameHistory::lossesToday()->with([
            'user' => function ($query) {
                $query
                    ->where('isAffiliate', false)
                    ->where('role', 'user');
            }
        ])->sum('finalAmount');
        $lossesLast30Days = GameHistory::lossesLast30Days()->with([
            'user' => function ($query) {
                $query
                    ->where('isAffiliate', false)
                    ->where('role', 'user');
            }
        ])->sum('finalAmount');
        $groupedDateLoss = GameHistory::dateGroupWin()->get();
        $groupedDateWin = GameHistory::dateGroupLoss()->get();
        $activeSessions = DB::table('sessions')
            ->where('last_activity', '>', now()->subMinutes(config('session.lifetime')))
            ->count();
        $totalUsers = User::all()->count();
        $lastUsers = User::latest('created_at')->where('isAffiliate', false)->where('role', 'user')->limit(30)->get();

        return Inertia::render('Admin/Dashboard', [
            'activeSessions' => $activeSessions,
            'totalUsers' => $totalUsers,
            'lastUsers' => $lastUsers,
            'payoutToday' => $winsToday * -1,
            'payoutLast30' => $winsLast30Days * -1,
            'payoutTotal' => $winsTotal * -1,
            'lossToday' => $lossesToday * -1,
            'lossLast30' => $lossesLast30Days * -1,
            'lossTotal' => $lossesTotal * -1,
            'groupedDateWin' => $groupedDateWin,
            'groupedDateLoss' => $groupedDateLoss,
        ]);
    }
    public function ggr()
    {
        // get env 
        $env = env('APP_GGR');
        return Inertia::render('Admin/GGR', [
            'GgrIsTrue' => $env,
        ]);
    }
}
