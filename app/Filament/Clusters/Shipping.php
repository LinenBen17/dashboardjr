<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Shipping extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Envíos';

    protected static ?string $clusterBreadcrumb = 'Envíos';
}
