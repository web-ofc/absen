<?php

namespace App\Providers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

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
    // Pastikan storage link sudah ada
    if (!Storage::disk('public')->exists('photos')) {
        Storage::disk('public')->makeDirectory('photos');
    }
}
}
