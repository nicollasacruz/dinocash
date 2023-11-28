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


Route::get('/afiliado', function () {
    return Inertia::render('Affiliates');
})->middleware(['auth', 'verified'])->name('user.affiliate');
Route::get('/saques', function () {
    return Inertia::render('Requests');
})->middleware(['auth', 'verified'])->name('user.requests');
Route::get('/depositos', function () {
    return Inertia::render('Deposits');
})->middleware(['auth', 'verified'])->name('user.deposits');

Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    
    Route::get('/usuarios', function () {
        return Inertia::render('Users', [
            'users' => User::all()
        ]);
    })->name('admin.usuarios');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings');
    Route::patch('/settings', [SettingController::class, 'update'])->name('admin.settings.edit');
    Route::get('/financeiro', [FinanceController::class, 'index'])->name('admin.financeiro');
    Route::get('/afiliados', [AffiliateController::class, 'index'])->name('admin.afiliados');
    Route::get('/saque', [WithdrawController::class, 'indexAdmin'])->name('admin.saque');
    Route::get('/deposito', [DepositController::class, 'indexAdmin'])->name('admin.deposito');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->prefix('user')->group(function () {
    Route::get('/deposit', [DepositController::class, 'indexUser'])->name('deposit.index');
    Route::post('/deposit', [DepositController::class, 'store'])->name('deposit.store');
    Route::patch('/deposit', [DepositController::class, 'update'])->name('deposit.update');
    Route::delete('/deposit', [DepositController::class, 'destroy'])->name('deposit.destroy');
});

require __DIR__ . '/auth.php';
