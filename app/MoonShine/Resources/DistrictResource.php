<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\District;
use ForestLynx\MoonShine\Fields\Decimal;
use Illuminate\Support\Facades\Request;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Fields\Number;
use MoonShine\Fields\Select;
use MoonShine\Fields\Text;

/**
 * @extends ModelResource<District>
 */
class DistrictResource extends ModelResource
{
    protected string $model = District::class;

    protected string $title = 'Districts';

    protected bool $createInModal = true; 
    protected bool $editInModal = true;  
    protected bool $detailInModal = true;

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
                Select::make('Tipo Circunscripción', 'name')
                    ->options([
                        'CE1' => 'Circunscripción 1',
                        'CE2' => 'Circunscripción 2'
                    ]) 
                    ->required(),
                Decimal::make('Salario', 'salary')
                    ->required(), 
                Number::make('Año', 'year')
                    ->required()
                    ->sortable(),
            ]),
        ];
    }

    /**
     * @param District $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}
