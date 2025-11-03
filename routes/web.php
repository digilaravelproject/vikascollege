<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\PdfViewerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrustController;
use App\Http\Middleware\VerifyPdfFile;
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

// Route to render the PDF viewer
// Route::get('/render-pdf', [PdfViewerController::class, 'show'])
//     ->middleware(VerifyPdfFile::class)
//     ->name('pdf.viewer');

require __DIR__ . '/auth.php'; // Breeze authentication routes
require __DIR__ . '/admin.php'; // Admin routes

// 🧩 Finally — the dynamic page slug route
Route::get('{slug}', [PageController::class, 'show'])
    ->where('slug', '^[a-zA-Z0-9\-_\/]+$')
    ->name('page.view');
