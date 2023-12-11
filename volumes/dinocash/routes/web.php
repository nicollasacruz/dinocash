<?php

use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\AffiliatePanelController;
use App\Http\Controllers\AffiliateWithdrawController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\WithdrawController;
use App\Http\Controllers\GameHistoryController;
use App\Models\GameHistory;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $userIdLogado = Auth::id();

    $rankedUsers = [];
    $ids = [];
    $position = 1;
    $usuarioLogadoInserido = false;

    $rankings = GameHistory::select(['userId', 'distance'])
        ->where('type', 'win')
        ->orderBy('distance', 'desc')
        ->limit(1000)
        ->get();

    foreach ($rankings as $ranking) {
        if (count($rankedUsers) === 10){
            break;
        }
        $user = User::find($ranking->userId);
        $email = $user->name;

        if ($ranking->userId == $userIdLogado) {
            $usuarioLogadoInserido = true;
        }
        if (in_array($ranking->userId, $ids)) {
            if (count($rankedUsers) === 10){
                break;
            }
            continue;
        }

        $rankedUsers[] = [
            'posicao' => $position,
            'email' => $email,
            'distancia' => $ranking->distance,
        ];
        $ids[] = $ranking->userId;

        $position++;
    }

    if (!$usuarioLogadoInserido && $userIdLogado) {
        $userLogado = User::find($userIdLogado);
        $emailLogado = $userLogado->name;

        $posicaoUsuarioLogado = GameHistory::where('type', 'win')
            ->orderBy('distance', 'desc')
            ->pluck('userId')
            ->search($userLogado->id);

        $distance = GameHistory::select(['distance'])
            ->where('type', 'win')
            ->where('userId', $userLogado->id)
            ->orderBy('distance', 'desc')
            ->first();

        $rankedUsers[9] = [
            'posicao' => $posicaoUsuarioLogado + 1,
            'email' => $emailLogado,
            'distancia' => $distance ? $distance->distance : 0,
        ];
        $ids[] = $userLogado->id;

    }
    $wallet = 0;
    if (Auth::check()) {
        $userLogado = User::find($userIdLogado);
        $wallet = $userLogado->wallet;
    }

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'rankedUsers' => $rankedUsers,
        'wallet' => $wallet ,
    ]);
})->name('homepage');

Route::get('/ref/{invitation_link}', function ($invitation_link) {
    Session::put('invitation_link', $invitation_link);
    Session::put('invitation_link_expires_at', Carbon::now()->addWeek());

    return redirect()->route('register');
});

Route::get('language/{language}', function ($language) {
    session()->put('locale', $language);
    dump(app()->getLocale());
    return;
})->name('language');

//     JOGAR
Route::middleware(['auth', 'verified', 'singleSession'])->group(function () {
    Route::get('/jogar', [GameHistoryController::class, 'play'])->name('user.play');
    Route::post('/jogar', [GameHistoryController::class, 'store'])->name('user.play.store');
    Route::patch('/jogar', [GameHistoryController::class, 'update'])->name('user.play.update');
});
//     JOGAR

//       ADMIN GROUP
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', function () {
        return Redirect::route('admin.dashboard');
    })->name('admin');
    Route::get('/usuarios', function () {
        return Inertia::render('Admin/Users', [
            'users' => User::all()
        ]);
    })->name('admin.usuarios');
    Route::patch('/usuarios', [ProfileController::class, 'modalUserUpdate'])->name('admin.usuarios.update');
    Route::get('/listAffiliateHistory', [ProfileController::class, 'listAffiliateHistory'])->name('admin.usuarios.comissao');
    Route::get('/listGameHistory', [ProfileController::class, 'listGameHistory'])->name('admin.usuarios.jogadas');
    Route::get('/listTransactions', [ProfileController::class, 'listTransactions'])->name('admin.usuarios.saques');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/financeiro', [FinanceController::class, 'index'])->name('admin.financeiro');
    Route::get('/ggr', [DashboardController::class, 'ggr'])->name('admin.ggr');

    Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings');
    Route::patch('/settings', [SettingController::class, 'update'])->name('admin.settings.update');
    Route::post('/settings/changePayout', [SettingController::class, 'changePayout'])->name('admin.settings.payout');

    Route::prefix('afiliados')->group(function () {
        Route::get('/', [AffiliateController::class, 'index'])->name('admin.afiliados');
        Route::patch('/', [AffiliateController::class, 'update'])->name('admin.afiliados.update');
        Route::delete('/', [AffiliateController::class, 'destroy'])->name('admin.afiliados.destroy');
        Route::post('/listAffiliateHistory', [AffiliateController::class, 'listAffiliateHistory'])->name('admin.afiliados.comissao');
        Route::post('/listGameHistory', [AffiliateController::class, 'listGameHistory'])->name('admin.afiliados.jogadas');
        Route::post('/listTransactions', [AffiliateController::class, 'listTransactions'])->name('admin.afiliados.saques');
        Route::post('/saque/aprovar', [AffiliateWithdrawController::class, 'aprove'])->name('admin.saque.afiliados.aprovar');
        Route::post('/saque/rejeitar', [AffiliateWithdrawController::class, 'reject'])->name('admin.saque.afiliados.rejeitar');
    });

    Route::get('/saque', [WithdrawController::class, 'indexAdmin'])->name('admin.saque');
    Route::post('/saque/aprovar', [WithdrawController::class, 'aprove'])->name('admin.saque.aprovar');
    Route::post('/saque/rejeitar', [WithdrawController::class, 'reject'])->name('admin.saque.rejeitar');
    Route::get('/deposito', [DepositController::class, 'indexAdmin'])->name('admin.deposito');
});

//       USER PAINEL
Route::middleware(['auth', 'verified'])->prefix('user')->group(function () {
    Route::get('/', function () {
        return Redirect::route('user.historico');
    })->name('user');

    Route::get('/perfil', [ProfileController::class, 'edit'])->name('user.edit');
    Route::patch('/perfil', [ProfileController::class, 'update'])->name('user.update');
    Route::delete('/perfil', [ProfileController::class, 'destroy'])->name('user.destroy');
    
    Route::get('/historico', [ProfileController::class, 'gameHistory'])->name('user.historico');
    Route::get('/movimentacao', [ProfileController::class, 'userWithdrawsAndDeposits'])->name('user.movimentacao');
    
    Route::get('/saque', [WithdrawController::class, 'indexUser'])->name('user.saque');
    Route::post('/saque', [WithdrawController::class, 'store'])->name('user.saque.store');
    
    Route::get('/deposito', [DepositController::class, 'index'])->name('user.deposito');
    Route::post('/deposito', [DepositController::class, 'store'])->name('user.deposito.store');
    Route::patch('/deposito', [DepositController::class, 'update'])->name('user.deposito.update');
    Route::delete('/deposito', [DepositController::class, 'destroy'])->name('user.deposito.destroy');
    
    Route::get('/alterar-senha', function () {
        return Inertia::render('User/ChangePassword');
    })->name('user.alterar_senha');
    Route::get('/suporte', function () {
        return Inertia::render('User/Suport');
    })->name('user.suporte');
    Route::get('/lastGame', function () {
        $user = User::find(Auth::user()->id);
        $amount = $user->gameHistories->where('type', 'pending')->first();
        $amount->update([
            'type' => 'gaming',
        ]);
        return response()->json([
            'userId' => $user->id,
            'amount' => $amount ? (float)$amount->amount : 0,
        ]);
    })->name('user.lastGame');
});


//       AFFILIATE PANEL
Route::middleware(['auth', 'verified', 'isAffiliate'])->prefix('afiliados')->group(function () {
    Route::get('/', [AffiliatePanelController::class, 'dashboardAffiliate'])->name('afiliado.index');
    Route::get('/dashboard', [AffiliatePanelController::class, 'dashboardAffiliate'])->name('afiliado.dashboard');
    Route::get('/saques', [AffiliatePanelController::class, 'withdrawsAffiliate'])->name('afiliado.saques');
    Route::post('/saques', [AffiliateWithdrawController::class, 'store'])->name('afiliado.saques.store');
    Route::get('/historico', [AffiliatePanelController::class, 'historyAffiliate'])->name('afiliado.historico');
    Route::get('/faturas', [AffiliatePanelController::class, 'invoicesAffiliate'])->name('afiliado.faturas');
});
Route::post('/', [ProfileController::class, 'edit'])->name('logout');

Route::post('callback', [DepositController::class, 'webhook'])->name('webhook.teste');

require __DIR__ . '/auth.php';
