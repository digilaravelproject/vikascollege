<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrustController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('homepage');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route::get('/the-trust', function () {
//     return "The Trust";
// })->name('the-trust');

Route::get('/the-trust/{slug?}', [TrustController::class, 'index'])->name('trust.index');
require __DIR__ . '/auth.php'; // Breeze authentication routes
require __DIR__ . '/admin.php'; // Admin routes
