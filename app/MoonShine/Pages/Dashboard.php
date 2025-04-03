<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Models\Employee;
use App\Models\Installments;
use MoonShine\Pages\Page;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Flex;
use MoonShine\Decorations\Grid;
use MoonShine\Metrics\DonutChartMetric;
use MoonShine\Metrics\ValueMetric;

class Dashboard extends Page
{
    /**
     * @return array<string, string>
     */
    public function breadcrumbs(): array
    {
        return [
            '#' => $this->title()
        ];
    }

    public function title(): string
    {
        return $this->title ?: 'Dashboard';
    }

    /**
     * @return list<MoonShineComponent>
     */
    public function components(): array
    {
        return [
            Grid::make([
                Column::make([
                    ValueMetric::make('Employees')
                        ->value(Employee::count())
                        ->icon('heroicons.user-group'),
                ])->columnSpan(6),

                Column::make([
                    DonutChartMetric::make('Pending Loans share vs Paid Loans share')
                        ->values([
                            'Pending Loans' => Installments::where('status', 0)->count(),
                            'Paid Loans' => Installments::where('status', 1)->count()
                        ])
                ])->columnSpan(6),
            ])
        ];
    }
}
