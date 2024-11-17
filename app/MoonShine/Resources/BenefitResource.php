<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Benefit;
use Illuminate\Support\Facades\Request;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Fields\Text;

/**
 * @extends ModelResource<Benefit>
 */
class BenefitResource extends ModelResource
{
    protected string $model = Benefit::class;

    protected string $title = 'Benefits';
    
    protected bool $createInModal = true; 
    protected bool $editInModal = true;  
    protected bool $detailInModal = true;

    protected bool $withPolicy = false;

    protected int $itemsPerPage = 10;
    
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
            Block::make([
                ID::make()->sortable(),
                Text::make('Nombre del Beneficio', 'name')
            ]),
        ];
    }

    /**
     * @param Benefit $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}
