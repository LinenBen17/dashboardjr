<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vacation;
use Carbon\Carbon;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Request;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Buttons\CreateButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Grid;
use MoonShine\Fields\Date;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Number;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Select;
use MoonShine\Fields\Text;
use MoonShine\Fields\Textarea;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Handlers\ImportHandler;
use MoonShine\TypeCasts\ModelCast;

/**
 * @extends ModelResource<Vacation>
 */
class VacationResource extends ModelResource
{
    protected string $model = Vacation::class;

    protected string $title = 'Vacations';

    protected bool $createInModal = true;
    protected bool $editInModal = true;
    protected bool $detailInModal = true;

    protected int $itemsPerPage = 10;

    protected array $assets = [
        'assets/js/vacationResource.js',
        'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js',
    ];

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
    public function getActiveActions(): array
    {
        return ['view'];
    }
    protected function modifyCreateButton(ActionButton $button): ActionButton
    {
        return $button->emptyHidden();
    }
    /**
     * @return list<MoonShineComponent|Field>
     */
    public function indexButtons(): array
    {
        return [
            ActionButton::make(
                'PDF',
                fn(Model $item) => route('vacations.vacation_format', $item->getKey())
            )
                ->blank()
                ->icon('heroicons.document-text'),

            ActionButton::make(
                '',
                fn(Model $item) => route('vacations.delete', $item->getKey())
            )
                ->withConfirm(
                    'Confirmación',
                    'Se eliminará el registro y se restarán estos días al periodo correspondiente. ¿Estás seguro?',
                    'Sí, Eliminar.',
                    method: 'DELETE'
                )
                ->icon('heroicons.outline.trash')
                ->error(),
        ];
    }
    public function indexFields(): array
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
                Date::make('Fecha de Solicitud', 'request_date')
                    ->required()
                    ->format('d/m/Y'),
                BelongsTo::make(
                    'Periodo',
                    'vacationHistory',
                    fn($item) => "$item->year"
                )->required(),
                Date::make('Fecha de Inicio', 'start_date')
                    ->required()
                    ->format('d/m/Y'),
                Date::make('Fecha de Finalización', 'end_date')
                    ->required()
                    ->format('d/m/Y'),
                Number::make('Días solicitados', 'days_requested')->required(),
                BelongsTo::make('Tipo de Vacación', 'vacationType',)->required(),
                Textarea::make('Descripción', 'comments',)->required(),
            ]),
        ];
    }
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
                Number::make('Año a Gozar', 'request_year')->required(),
                Date::make('Fecha de Solicitud', 'request_date')->required(),
                Date::make('Fecha de Inicio', 'start_date')->required(),
                Date::make('Fecha de Finalización', 'end_date')->required(),
                Number::make('Días solicitados', 'days_requested')->required(),
                BelongsTo::make('Tipo de Vacación', 'vacationType',)->required(),
                Textarea::make('Descripción', 'comments',)->required(),
            ]),
        ];
    }

    public function actions(): array
    {
        return [
            ActionButton::make(
                label: 'Create',
            )->icon('heroicons.plus')
                ->primary()
                ->inModal(
                    title: fn() => 'Ingreso de Préstamos',
                    content: fn() =>
                    FormBuilder::make()
                        ->action(route('vacations.store'))
                        ->method('POST')
                        ->fields([
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
                                Number::make('Año a Gozar', 'request_year')->required(),
                                Date::make('Fecha de Solicitud', 'request_date')->required(),
                                Date::make('Fecha de Inicio', 'start_date')->required(),
                                Date::make('Fecha de Finalización', 'end_date')->required(),
                                Number::make('Días solicitados', 'days_requested')
                                    ->required(),
                                BelongsTo::make(
                                    'Tipo de Vacación',
                                    'vacationType',
                                    fn($item) => "$item->name"
                                )->required(),
                                Textarea::make('Descripción', 'comments',)->required(),
                            ]),
                        ])
                        ->cast(ModelCast::make(Vacation::class))
                        ->submit(label: 'Guardar', attributes: ['class' => 'btn-primary']),
                    //->customAttributes(['target' => '_blank']),
                    async: false
                )
                ->async(callback: 'createVacation'),
        ];
    }

    /**
     * @param Vacation $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}
