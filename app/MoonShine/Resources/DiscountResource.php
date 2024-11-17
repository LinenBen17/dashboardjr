<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Discount;
use ForestLynx\MoonShine\Fields\Decimal;
use Illuminate\Support\Facades\Request;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Grid;
use MoonShine\Fields\Date;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Select;
use MoonShine\Fields\Text;
use MoonShine\Fields\Textarea;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Handlers\ImportHandler;

/**
 * @extends ModelResource<Discount>
 */
class DiscountResource extends ModelResource
{
    protected string $model = Discount::class;

    protected string $title = 'Discounts';

    protected bool $createInModal = true; 
    protected bool $editInModal = true;  
    protected bool $detailInModal = true;

    protected int $itemsPerPage = 10;

    public function import(): ?ImportHandler
    {
        return ImportHandler::make('Importar');
    } 

    public function export(): ?ExportHandler
    {
        return ExportHandler::make('Exportar');
    }

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
                BelongsTo::make(
                    'Empleado',
                    'employees',
                    fn($item) => "$item->name $item->last_name"
                )->searchable()
                    ->nullable()
                    ->useOnImport(fromRaw: static fn(string $raw, $ctx) => $raw)
                    ->showOnExport()
                    ->required(),
                Select::make('Tipo', 'type')
                    ->options([
                        'anticipo' => 'Anticipo',
                        'ausencia' => 'Ausencia',
                        'otro' => 'Otros',
                    ])
                    ->nullable(),
                Date::make('Fecha del Descuento', 'date')
                    ->useOnImport(fromRaw: static fn(string $raw, $ctx) => $raw)
                    ->showOnExport()
                    ->required(),
                Decimal::make('Monto', 'amount')
                    ->default('00.01')
                    ->required(),
                Textarea::make('Obeservaciones', 'comments')
                    ->useOnImport(fromRaw: static fn(string $raw, $ctx) => $raw)
                    ->showOnExport()
                    ->required(),
            ]),
        ];
    }

    public function actions(): array 
    {
        return [
            ActionButton::make(
                label: 'Préstamos',
            )->success()
            ->inModal(
                title: fn() => 'Ingreso de Préstamos',
                content: fn() => 
                    FormBuilder::make()
                        ->action(route('storeBono14'))
                        ->method('POST')
                        ->fields([
                            Block::make([
                                Grid::make([
                                    Column::make([
                                        Date::make('Del:', 'date_from'),
                                        Date::make('Al:', 'date_to') 
                                    ])
                                ])
                            ])
                        ]),
                async: false
            ),
        ];
    }

    /**
     * @param Discount $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}
