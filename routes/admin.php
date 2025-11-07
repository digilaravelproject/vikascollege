<?php

use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PageBuilderController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\TrustSectionController;
use App\Http\Controllers\Admin\WebsiteSettingController;
use App\Http\Controllers\Admin\HomepageSetupController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\EventCategoryController;
use App\Http\Controllers\EventItemController;
use App\Http\Controllers\AcademicCalendarController;
use App\Http\Controllers\Admin\CacheController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\GalleryCategoryController;
use App\Http\Controllers\GalleryImageController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\WhyChooseUsController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

// Authentication
Route::get('admin', [AuthenticatedSessionController::class, 'create']);
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {

    Route::get('/dashboard', function () {
        $pendingTestimonials = \App\Models\Testimonial::where('status', false)->count();
        $upcomingEvents = \App\Models\EventItem::where('event_date', '>=', now())->count();
        $activeAnnouncements = \App\Models\Announcement::where('status', true)->count();
        return view('admin.dashboard', compact('pendingTestimonials', 'upcomingEvents', 'activeAnnouncements'));
    })->name('dashboard');
    Route::get('/roles-permissions', [RolePermissionController::class, 'index'])->name('roles-permissions.index');
    Route::post('/roles-permissions/assign', [RolePermissionController::class, 'assign'])->name('roles-permissions.assign');
    Route::post('/roles-permissions/create-role', [RolePermissionController::class, 'createRole'])->name('roles-permissions.create-role');
    Route::post('/roles-permissions/create-permission', [RolePermissionController::class, 'createPermission'])->name('roles-permissions.create-permission');
    Route::resource('menus', MenuController::class);
    Route::post('/menus/{menu}/toggle-status', [MenuController::class, 'toggleStatus'])->name('menus.toggle-status');
    Route::get('/website-settings', [WebsiteSettingController::class, 'index'])->name('website-settings.index');
    Route::post('/website-settings', [WebsiteSettingController::class, 'update'])->name('website-settings.update');
    Route::post('website-settings/delete-media', [WebsiteSettingController::class, 'deleteBannerMedia'])->name('website-settings.delete-media');

    // Homepage Setup
    Route::get('/homepage-setup', [HomepageSetupController::class, 'index'])->name('homepage.index');
    Route::post('/homepage-setup/save', [HomepageSetupController::class, 'save'])->name('homepage.save');
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

        // CRUD pages (index, create, edit, delete)
        Route::get('/', [PageBuilderController::class, 'index'])->name('index');
        Route::get('/create', [PageBuilderController::class, 'create'])->name('create');
        Route::post('/store', [PageBuilderController::class, 'store'])->name('store');
        Route::get('/edit/{page}', [PageBuilderController::class, 'edit'])->name('edit');
        Route::post('/update/{page}', [PageBuilderController::class, 'update'])->name('update');
        Route::delete('/delete/{page}', [PageBuilderController::class, 'destroy'])->name('delete');
        Route::post('{page}/toggle-status', [PageBuilderController::class, 'toggleStatus'])
            ->name('toggleStatus');

        // 🧱 Page Builder (Elementor-style)
        Route::get('/builder/{page}', [PageBuilderController::class, 'builder'])->name('builder');

        // Save builder data (HTML, CSS, JSON, etc.)
        Route::post('/builder/save/{page}', [PageBuilderController::class, 'saveBuilder'])->name('builder.save');

        // AJAX media uploads (for images, videos, etc.)
        Route::post('/builder/upload/{page}', [PageBuilderController::class, 'uploadMedia'])->name('builder.upload');
        Route::post('/builder/upload-delete', [PageBuilderController::class, 'deleteUploadedMedia'])->name('builder.upload.delete');
    });

    // Content Modules
    Route::resource('announcements', AnnouncementController::class)->except(['show'])->names('announcements');
    Route::resource('event-categories', EventCategoryController::class)->except(['show'])->names('event-categories');
    Route::resource('event-items', EventItemController::class)->except(['show'])->names('event-items');
    Route::resource('academic-calendar', AcademicCalendarController::class)->except(['show'])->names('academic-calendar');
    Route::resource('gallery-categories', GalleryCategoryController::class)->except(['show'])->names('gallery-categories');
    Route::resource('gallery-images', GalleryImageController::class)->except(['show'])->names('gallery-images');
    Route::resource('testimonials', TestimonialController::class)->except(['show'])->names('testimonials');
    Route::resource('why-choose-us', WhyChooseUsController::class)->except(['show'])->names('why-choose-us');

    // Notifications
    Route::get('notifications/list-active-featured', [NotificationController::class, 'listActiveFeatured'])
        ->name('notifications.list-active-featured');
    Route::resource('notifications', NotificationController::class)->except(['show'])->names('notifications');
    Route::post('notifications/{notification}/toggle-status', [NotificationController::class, 'toggleStatus'])->name('notifications.toggle-status');
    Route::post('notifications/{notification}/toggle-featured', [NotificationController::class, 'toggleFeatured'])->name('notifications.toggle-featured');
    Route::post('notifications/{notification}/toggle-feature-on-top', [NotificationController::class, 'toggleFeatureOnTop'])->name('notifications.toggle-feature-on-top');

    // Media Managemnet
    Route::get('/media', [MediaController::class, 'index'])->name('media.index');
    Route::post('/media/upload', [MediaController::class, 'store'])->name('media.store');
    Route::post('/media/delete', [MediaController::class, 'destroy'])->name('media.destroy');

    // Cache
    Route::get('/cache-management', [CacheController::class, 'index'])->name('cache.index');
    Route::get('/cache-clear-all', [CacheController::class, 'clearAllCache'])->name('cache.clear-all');
    Route::get('/cache-re-optimize', [CacheController::class, 'reOptimizeApp'])->name('cache.re-optimize');
});
