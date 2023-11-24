<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\ProfileController;
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
});

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

Route::get('/dashboard', function () {

    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/finance', function () {
    return Inertia::render('Finances');
})->middleware(['auth', 'verified'])->name('finance');
Route::get('/users', function () {
    return Inertia::render('Users');
})->middleware(['auth', 'verified'])->name('users');
Route::get('/affiliates', function () {
    return Inertia::render('Affiliates');
})->middleware(['auth', 'verified'])->name('affiliates');
Route::get('/requests', function () {
    return Inertia::render('Requests');
})->middleware(['auth', 'verified'])->name('requests');
Route::get('/deposits', function () {
    return Inertia::render('Deposits');
})->middleware(['auth', 'verified'])->name('deposits');

Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/financeiro', function () {
        return Inertia::render('Financeiro');
    })->name('admin.financeiro');

    Route::get('/usuarios', function () {
        return Inertia::render('Usuarios');
    })->name('admin.usuarios');

    Route::get('/afiliados', function () {
        return Inertia::render('Afiliados');
    })->name('admin.afiliados');

    Route::get('/saque', function () {
        return Inertia::render('Saque');
    })->name('admin.saque');

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
