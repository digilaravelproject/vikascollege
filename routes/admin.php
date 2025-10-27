<?php

use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\RolePermissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    Route::get('/roles-permissions', [RolePermissionController::class, 'index'])->name('roles-permissions.index');
    Route::post('/roles-permissions/assign', [RolePermissionController::class, 'assign'])->name('roles-permissions.assign');
    Route::post('/roles-permissions/create-role', [RolePermissionController::class, 'createRole'])->name('roles-permissions.create-role');
    Route::post('/roles-permissions/create-permission', [RolePermissionController::class, 'createPermission'])->name('roles-permissions.create-permission');
    Route::resource('menus', MenuController::class);
});
