<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Post;
use Illuminate\Support\Facades\Request;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Components\Title;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Grid;
use MoonShine\Fields\Text;
use MoonShine\Metrics\ValueMetric;

/**
 * @extends ModelResource<Post>
 */
class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Articles';

    protected bool $createInModal = true; 
 
    protected bool $editInModal = true; 
 
    protected bool $detailInModal = false;

    protected bool $withPolicy = true;

    public function redirectAfterSave(): string
    {
        $referer = Request::header('referer');
        return $referer ?: '/';
    }

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            Grid::make([
                Column::make([
                    ID::make()->sortable(),
                    Text::make('Title'),
                ])->columnSpan(2),
                Title::make('A'),
                Column::make([
                    Text::make('Content'),
                ])->columnSpan(4)
            ]),
        ];
    }

    /**
     * @param Post $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }

    public function metrics(): array
	{
        $totalPost = Post::count();
		return [
            Grid::make([
                Column::make([
                    ValueMetric::make('Post Count')
                    ->value($totalPost)
                    ->icon('heroicons.users')
                ])->columnSpan(5),
                Column::make([
                    ValueMetric::make('Post Count')
                    ->value($totalPost)
                    ->icon('heroicons.users')
                ])->columnSpan(7),
            ])
        ];
	}
}
