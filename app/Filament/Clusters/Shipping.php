<?php

namespace App\Filament\Clusters;

use App\Filament\Pages\ShipmentReports;
use Filament\Clusters\Cluster;

class Shipping extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Envíos';

    protected static ?string $clusterBreadcrumb = 'Envíos';

    //set index page for the cluster
    protected static ?string $indexPage = \App\Filament\Pages\HomeShippingCluster::class;
}
