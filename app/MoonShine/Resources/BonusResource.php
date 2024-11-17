<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Bonus;
use App\Models\DateBenefit;
use ForestLynx\MoonShine\Fields\Decimal;
use Illuminate\Support\Facades\Request;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Components\Layout\Flash;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Fragment;
use MoonShine\Decorations\Grid;
use MoonShine\Enums\JsEvent;
use MoonShine\Fields\Date;
use MoonShine\Fields\DateRange;
use MoonShine\Fields\Number;
use MoonShine\Fields\Range;
use MoonShine\Fields\RangeSlider;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Text;
use MoonShine\Fields\Textarea;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Handlers\ImportHandler;
use MoonShine\Metrics\ValueMetric;
use MoonShine\MoonShineUI;
use MoonShine\Support\AlpineJs;

/**
 * @extends ModelResource<Bonus>
 */
class BonusResource extends ModelResource
{
    protected string $model = Bonus::class;

    protected string $title = 'Bonus';

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
                Date::make('Fecha del Bono', 'date')
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
    /**
     * @param Bonus $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
    
    public function actions(): array 
    {
        return [
            ActionButton::make(
                label: 'Bono 14',
            )->warning()
            ->inModal(
                title: fn() => 'Creación Planilla Bono 14',
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

    public function metrics(): array
	{
        return [
            Flash::make(key: 'successSave', type: 'success', withToast: true, removable: true),
            Flash::make(key: 'failSave', type: 'error', withToast: true, removable: false),
        ];
	}
}
