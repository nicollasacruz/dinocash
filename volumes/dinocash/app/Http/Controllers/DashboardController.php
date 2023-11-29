<?php

namespace App\Http\Controllers;

use App\Models\GameHistory;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $winsToday = GameHistory::winsToday()->sum('finalAmount');
        $winsLast30Days = GameHistory::winsLast30Days()->sum('finalAmount');
        $winsTotal = GameHistory::winsTotal()->sum('finalAmount');
        $lossesToday = GameHistory::lossesToday()->sum('finalAmount');
        $lossesLast30Days = GameHistory::lossesLast30Days()->sum('finalAmount');
        $lossesTotal = GameHistory::lossesTotal()->sum('finalAmount');
        $groupedDateWin = GameHistory::dateGroupWin()->get();
        $groupedDateLoss = GameHistory::dateGroupLoss()->get();
        dd($groupedDateWin);
        $activeSessions = DB::table('sessions')
            ->where('last_activity', '>', now()->subMinutes(config('session.lifetime')))
            ->count();
        $totalUsers = User::all()->count();
        $lastUsers = User::latest('created_at')->limit(30)->get();

        return Inertia::render('Dashboard', [
            'activeSessions' => $activeSessions,
            'totalUsers' => $totalUsers,
            'lastUsers' => $lastUsers,
            'payoutToday' => $winsToday,
            'payoutLast30' => $winsLast30Days,
            'payoutTotal' => $winsTotal,
            'lossToday' => $lossesToday,
            'lossLast30' => $lossesLast30Days,
            'lossTotal' => $lossesTotal,
            'groupedDateWin' => $groupedDateWin,
            'groupedDateLoss' => $groupedDateLoss,
        ]);
    }
}
