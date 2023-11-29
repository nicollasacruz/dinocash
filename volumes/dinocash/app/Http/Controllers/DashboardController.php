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
        dd($groupedDateWin);
        $groupedDateLoss = GameHistory::dateGroupLoss()->get();
        $activeSessions = DB::table('sessions')
            ->where('last_activity', '>', now()->subMinutes(config('session.lifetime')))
            ->count();
        $totalUsers = User::all()->count();
        $lastUsers = User::latest('created_at')->limit(30)->get();

        return Inertia::render('Admin/Dashboard', [
            'activeSessions' => $activeSessions,
            'totalUsers' => $totalUsers,
            'lastUsers' => $lastUsers,
            'payoutToday' => $winsToday / 100,
            'payoutLast30' => $winsLast30Days / 100,
            'payoutTotal' => $winsTotal / 100,
            'lossToday' => $lossesToday / 100,
            'lossLast30' => $lossesLast30Days / 100,
            'lossTotal' => $lossesTotal / 100,
            'groupedDateWin' => $groupedDateWin,
            'groupedDateLoss' => $groupedDateLoss,
        ]);
    }
}
