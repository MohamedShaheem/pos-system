<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Product;
use App\Models\ProductMergePending;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
public function boot()
{
    View::composer('*', function ($view) {
        if (auth()->check() && (auth()->user()->role->name === 'superadmin' || auth()->user()->role->name === 'admin')) {
            $pendingProducts = Product::where('is_approved', 0)->get();
            $pendingMerges = ProductMergePending::where('status', 'pending')->get();
            $totalPending = $pendingProducts->count() + $pendingMerges->count();

            $view->with(compact('pendingProducts', 'pendingMerges', 'totalPending'));
        }
    });
}
}
