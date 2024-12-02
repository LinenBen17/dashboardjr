<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Model;
use App\Models\Installments;
use ForestLynx\MoonShine\Fields\Decimal;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Fields\Checkbox;
use MoonShine\Fields\Date;
use MoonShine\Fields\Enum;
use MoonShine\Fields\Number;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Text;

/**
 * @extends ModelResource<Installments>
 */
class InstallmentsResource extends ModelResource
{
    protected string $model = Installments::class;

    protected string $title = 'Installments';

    protected bool $createInModal = true;
    protected bool $detailInModal = true;

    public function getActiveActions(): array 
    {
        return ['view'];
    }

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            Block::make([
                ID::make()
                ->sortable(
                    fn(Builder $query) => $query
                ),
                BelongsTo::make(
                    'No. Préstamo',
                    'loan',  
                ),
                Number::make('No. Cuota', 'no_installment'),
                Decimal::make('Monto de la Cuota', 'amount'),
                Checkbox::make('Status', 'status'),
                Date::make('Fecha de Cobro', 'billing_date')->format('d-m-Y'),
            ]),
        ];
    }

    /**
     * @param Installments $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}
