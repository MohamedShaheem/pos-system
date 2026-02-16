<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\GoldRate;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Share gold rate data with the admin layout
        View::composer(['layouts.user', 'layouts.admin', 'layouts.staff'], function ($view) {
            $goldRates = GoldRate::whereIn('name', ['22K', '24K'])
                ->orderByRaw("FIELD(name, '22K', '24K') ASC")
                ->get();

            $view->with('goldRates', $goldRates);
        });
    }

    /**
     * Register services.
     */
    public function register()
    {
        //
    }
}
