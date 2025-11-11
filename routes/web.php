<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrustController;
use Illuminate\Support\Facades\Route;

// 1. Root Redirection: '/' will redirect to '/vikas'
Route::redirect('/', '/vikas');

// 2. EXCLUDED ROUTES: Dashboard, Profile, Auth, and Admin routes
// These routes will NOT have the 'vikas' prefix.

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php'; // Breeze authentication routes
require __DIR__ . '/admin.php'; // Admin routes

// 3. PUBLIC ROUTES GROUP: Applying the 'vikas' prefix to all public URLs
Route::prefix('vikas')->group(function () {

    // URL: /vikas
    Route::get('/', function () {
        return view('homepage');
    })->name('homepage');

    // URL: /vikas/the-trust
    Route::get('/the-trust/{slug?}', [TrustController::class, 'index'])->name('trust.index');

    // URL: /vikas/{slug} (Dynamic Pages)
    // Note: Since the prefix is 'vikas', we only need '/{slug}' here.
    Route::get('/{slug}', [PageController::class, 'show'])
        ->where('slug', '^[a-zA-Z0-9\-_\/]+$')
        ->name('page.view');
});
