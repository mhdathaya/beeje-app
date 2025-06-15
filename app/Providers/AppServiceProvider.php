<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // Add this import
use App\Models\Product; // Add this import
use Illuminate\Support\Facades\DB; // Add this import

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
        // Share categories with all views
        View::composer('*', function ($view) {
            $categories = Product::select('category', DB::raw('count(*) as product_count'))
                                 ->groupBy('category')
                                 ->get();
            $view->with('categories', $categories);
        });
    }
}
