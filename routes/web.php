<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GHLContactController;
use App\Http\Controllers\GHLSettingsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});


//Route::get('/dashboard', function () {
//    return view('dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::put('/ghl-settings', [GHLSettingsController::class, 'update'])->name('ghl.settings.update');

    Route::get('/contacts/{id}/edit', [GHLContactController::class, 'edit'])->name('contacts.edit');
    Route::put('/contacts/{id}', [GHLContactController::class, 'update'])->name('contacts.update');
    Route::delete('/contacts/{id}', [GHLContactController::class, 'destroy'])->name('contacts.destroy');

    Route::get('/contacts/create', [DashboardController::class, 'create'])->name('contacts.create');
    Route::post('/contacts', [GHLContactController::class, 'store'])->name('contacts.store');

    Route::get('/account', [DashboardController::class, 'account'])->name('account');
});

require __DIR__.'/auth.php';
