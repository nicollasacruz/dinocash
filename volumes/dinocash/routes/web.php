<?php

use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\WithdrawController;
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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
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

Route::get('php_info', function () {
    echo 'ssaad';
    return dd(phpinfo());
})->name('language');


Route::get('/afiliado', function () {
    return Inertia::render('Admin/Affiliates');
})->middleware(['auth', 'verified'])->name('user.affiliate');
Route::get('/saques', function () {
    return Inertia::render('Requests');
})->middleware(['auth', 'verified'])->name('user.requests');
Route::get('/depositos', function () {
    return Inertia::render('Deposits');
})->middleware(['auth', 'verified'])->name('user.deposits');

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', function () {
        return Redirect::route('admin.dashboard');
    })->name('admin');
    Route::get('/usuarios', function () {
        return Inertia::render('Admin/Users', [
            'users' => User::all()
        ]);
    })->name('admin.usuarios');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings');
    Route::patch('/settings', [SettingController::class, 'update'])->name('admin.settings.edit');
    Route::get('/financeiro', [FinanceController::class, 'index'])->name('admin.financeiro');

    Route::prefix('afiliados')->group(function () {
        Route::get('/', [AffiliateController::class, 'index'])->name('admin.afiliados');
        Route::patch('/', [AffiliateController::class, 'update'])->name('admin.afiliados.update');
        Route::delete('/', [AffiliateController::class, 'destroy'])->name('admin.afiliados.destroy');
        Route::post('/listAffiliateHistory', [AffiliateController::class, 'listAffiliateHistory'])->name('admin.afiliados.comissao');
        Route::post('/listGameHistory', [AffiliateController::class, 'listGameHistory'])->name('admin.afiliados.jogadas');
        Route::post('/listTransactions', [AffiliateController::class, 'listTransactions'])->name('admin.afiliados.saques');
    });

    Route::get('/saque', [WithdrawController::class, 'indexAdmin'])->name('admin.saque');
    Route::post('/saque/aprovar', [WithdrawController::class, 'aprove'])->name('admin.saque.aprovar');
    Route::post('/saque/rejeitar', [WithdrawController::class, 'reject'])->name('admin.saque.rejeitar');
    Route::get('/deposito', [DepositController::class, 'indexAdmin'])->name('admin.deposito');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/perfil', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/listAffiliateHistory', [ProfileController::class, 'listAffiliateHistory'])->name('profile.comissao');
    Route::post('/listGameHistory', [ProfileController::class, 'listGameHistory'])->name('profile.jogadas');
    Route::post('/listDeposits', [ProfileController::class, 'listDeposits'])->name('profile.depositos');
    Route::post('/listWithdraws', [ProfileController::class, 'listWithdraws'])->name('profile.saques');
});

Route::middleware(['auth'])->prefix('user')->group(function () {
    Route::get('/deposito', [DepositController::class, 'indexUser'])->name('deposit.index');
    Route::post('/deposito', [DepositController::class, 'store'])->name('deposit.store');
    Route::patch('/deposito', [DepositController::class, 'update'])->name('deposit.update');
    Route::delete('/deposito', [DepositController::class, 'destroy'])->name('deposit.destroy');
});

Route::post(env('SUITPAY_URL_WEBHOOK'), [DepositController::class, 'webhook'])->name('webhook.deposit');

Route::domain(env('APP_URL_API'))->middleware([])->group(function () {
    //
});


require __DIR__ . '/auth.php';
