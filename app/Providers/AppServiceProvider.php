<?php

namespace App\Providers;

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Schema;
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
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        /* FilamentAsset::register([
            //agregar jquery
            Js::make('jquery', 'https://code.jquery.com/jquery-3.6.0.min.js')->loadedOnRequest(),
            Js::make('custom-script', __DIR__ . '/../../resources/js/custom.js')->loadedOnRequest(),
        ]); */
    }
}
