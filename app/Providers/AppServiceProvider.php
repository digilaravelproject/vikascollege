<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // $menus = Menu::where('status', 1)
        //     ->whereNull('parent_id')
        //     ->with('children')
        //     ->orderBy('order')
        //     ->get();

        // View::share('menus', $menus);
    }
}
