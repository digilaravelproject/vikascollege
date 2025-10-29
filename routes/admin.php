<?php

use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PageBuilderController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\TrustSectionController;
use App\Http\Controllers\Admin\WebsiteSettingController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

// Authentication
Route::get('admin', [AuthenticatedSessionController::class, 'create']);
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    Route::get('/roles-permissions', [RolePermissionController::class, 'index'])->name('roles-permissions.index');
    Route::post('/roles-permissions/assign', [RolePermissionController::class, 'assign'])->name('roles-permissions.assign');
    Route::post('/roles-permissions/create-role', [RolePermissionController::class, 'createRole'])->name('roles-permissions.create-role');
    Route::post('/roles-permissions/create-permission', [RolePermissionController::class, 'createPermission'])->name('roles-permissions.create-permission');
    Route::resource('menus', MenuController::class);
    Route::post('/menus/{menu}/toggle-status', [MenuController::class, 'toggleStatus'])->name('menus.toggle-status');
    Route::get('/website-settings', [WebsiteSettingController::class, 'index'])->name('website-settings.index');
    Route::post('/website-settings', [WebsiteSettingController::class, 'update'])->name('website-settings.update');
    Route::resource('trust', TrustSectionController::class)
        ->except(['show', 'destroy']) // keep index, create, store, edit, update
        ->parameters([
            'trust' => 'trustSection'
        ])
        ->names('trust');

    Route::delete('trust/image/{image}', [TrustSectionController::class, 'destroyImage'])
        ->name('trust.image.destroy');

    Route::delete('trust/pdf/{trustSection}', [TrustSectionController::class, 'removePdf'])
        ->name('trust.pdf.remove');



    Route::prefix('pagebuilder')->name('pagebuilder.')->group(function () {
        Route::get('/', [PageBuilderController::class, 'index'])->name('index');
        Route::get('/create', [PageBuilderController::class, 'create'])->name('create');
        Route::post('/store', [PageBuilderController::class, 'store'])->name('store');
        Route::get('/edit/{page}', [PageBuilderController::class, 'edit'])->name('edit');
        Route::post('/update/{page}', [PageBuilderController::class, 'update'])->name('update');
        Route::delete('/delete/{page}', [PageBuilderController::class, 'destroy'])->name('delete');

        // Builder routes
        Route::get('/builder/{page}', [PageBuilderController::class, 'builder'])->name('builder');
        Route::post('/builder/save/{page}', [PageBuilderController::class, 'saveBuilder'])->name('builder.save');

        // AJAX upload/delete
        Route::post('/builder/upload/{page}', [PageBuilderController::class, 'uploadMedia'])->name('builder.upload');
        Route::post('/builder/upload-delete', [PageBuilderController::class, 'deleteUploadedMedia'])->name('builder.upload.delete');
    });
});
