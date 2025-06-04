<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Warehouse extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Bodega';

    protected static ?string $clusterBreadcrumb = 'Bodega';
}
