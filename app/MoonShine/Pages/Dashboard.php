<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Models\Post;
use MoonShine\Pages\Page;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Decorations\Grid;
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
        $totalPost = Post::count();
		return [
            Grid::make([
                ValueMetric::make('Post Count')
                    ->value($totalPost)
                    ->icon('heroicons.users'),
            ])
        ];
	}
}
