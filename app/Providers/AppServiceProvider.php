<?php

namespace App\Providers;

use Dvarilek\FilamentTableSelect\Components\Form\TableSelect;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\DB;
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

        // Solo si aún no está seteado (por si haces seed + cache)
        if (empty(config('filament-edit-profile.custom_fields.departament_id.options'))) {
            config([
                'filament-edit-profile.custom_fields.departament_id.options' =>
                DB::table('departaments')
                    ->pluck('name', 'id')
                    ->toArray(),
            ]);
        }
        if (empty(config('filament-edit-profile.custom_fields.agency_id.options'))) {
            config([
                'filament-edit-profile.custom_fields.agency_id.options' =>
                DB::table('agencies')
                    ->pluck('name', 'id')
                    ->toArray(),
            ]);
        }

        FilamentAsset::register([
            //agregar jquery
            Js::make('jquery', 'https://code.jquery.com/jquery-3.6.0.min.js')->loadedOnRequest(),
            Js::make('custom-script', asset('js/globalCustom.js')),
        ]);
    }
}
