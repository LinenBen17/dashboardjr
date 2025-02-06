<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Post;
use App\Models\User;
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
use MoonShine\Models\MoonshineUser;
use MoonShine\Models\MoonshineUserRole;
use Sweet1s\MoonshineRBAC\Traits\WithRolePermissions;

/**
 * @extends ModelResource<Post>
 */
class PostResource extends ModelResource
{
    use WithRolePermissions;

    protected string $model = Post::class;

    protected string $title = 'Articles';

    protected bool $createInModal = true;

    protected bool $editInModal = true;

    protected bool $detailInModal = false;

    public function redirectAfterSave(): string
    {
        $referer = Request::header('referer');
        return $referer ?: '/';
    }

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function indexButtons(): array
    {
        return [
            ActionButton::make(
                'PDF',
                fn(Model $item) => route('loans.loan_format', $item->getKey())
            )
                ->canSee(function () {
                    $user = User::permission('UserResource.viewAny')->first();
                    logger($user);
                    logger("Aqui");
                    return false;
                })
                ->blank()
                ->icon('heroicons.document-text'),

            ActionButton::make(
                '',
                fn(Model $item) => route('loans.delete', $item->getKey())
            )
                ->withConfirm(
                    'Confirmación',
                    'Esto eliminará el préstamo y todo lo relacionado con el mismo. ¿Estás seguro?',
                    'Sí, Eliminar.',
                )
                ->icon('heroicons.outline.trash')
                ->error()

        ];
    }

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
