<?php

namespace App\Filament\Pages;

use App\Filament\Clusters\Shipping;
use Filament\Pages\Page;

class HomeShippingCluster extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.home-shipping-cluster';

    protected static ?string $cluster = Shipping::class;

    protected static ?string $navigationLabel = 'Menú Envíos';
    protected static ?string $modelLabel = 'Menú Envíos';

    protected static ?int $navigationSort = 1;
}
