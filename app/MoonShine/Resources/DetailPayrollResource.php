<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\DetailPayroll;
use App\Models\District;
use App\Models\Employee;
use ForestLynx\MoonShine\Fields\Decimal;
use Illuminate\Support\Facades\Request;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Number;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Relationships\HasOne;
use MoonShine\Fields\Text;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Handlers\ImportHandler;
use Sweet1s\MoonshineRBAC\Traits\WithRolePermissions;

/**
 * @extends ModelResource<DetailPayroll>
 */
class DetailPayrollResource extends ModelResource
{
    use WithRolePermissions;

    protected string $model = DetailPayroll::class;

    protected string $title = 'DetailPayrolls';

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
                    ->required()
                    ->nullable()
                    ->useOnImport(fromRaw: static fn(string $raw, $ctx) => $raw)
                    ->showOnExport()
                    ->reactive(function (Fields $fields, ?int $employeeId): Fields {
                        if ($employeeId) {
                            $employee = Employee::find($employeeId);
                            $salary = District::where('year', now()->year)
                                ->where('name', ($employee->id_agency == 2) ? 'CE1' : 'CE2')
                                ->value('salary');
                            $district = District::where('year', now()->year)
                                ->where('name', ($employee->id_agency == 2) ? 'CE1' : 'CE2')
                                ->first();
                            return tap($fields, function ($fields) use ($salary, $district) {
                                $fields->findByColumn('regular_salaries')?->setValue($salary); // Correcto para el campo relacionado
                                $fields->findByColumn('district_id')?->setValue($district); // Relacionar el campo de distritos
                            });
                        }

                        // Retornar los campos sin cambios si no se seleccionó un empleado.
                        return $fields;
                    }),
                BelongsTo::make(
                    'Circunscripción',
                    'districts',
                    fn($item) => "$item->name $item->last_name"
                )->searchable()
                    ->required()
                    ->readonly()
                    ->nullable()
                    ->useOnImport(fromRaw: static fn(string $raw, $ctx) => $raw)
                    ->showOnExport()
                    ->reactive(),
                Decimal::make('Sueldo Ordinario', 'regular_salaries')
                    ->useOnImport(fromRaw: static fn(string $raw, $ctx) => $raw)
                    ->showOnExport()
                    ->default('0.00')
                    ->required()
                    ->reactive(),
                Decimal::make('Bonificación De Ley', 'bonus_of_law')
                    ->useOnImport(fromRaw: static fn(string $raw, $ctx) => $raw)
                    ->showOnExport()
                    ->default('250.00')
                    ->required(),
                Decimal::make('Bonificación Incentiva', 'incentive_bonus')
                    ->useOnImport(fromRaw: static fn(string $raw, $ctx) => $raw)
                    ->showOnExport()
                    ->default('0.00')
                    ->required()
                    ->reactive(),
                Decimal::make('Porcentaje IGSS', 'percentage_igss')
                    ->useOnImport(fromRaw: static fn(string $raw, $ctx) => $raw)
                    ->showOnExport()
                    ->default('0.00')
                    ->required(),
                Decimal::make('Porcentaje ISR', 'percentage_isr')
                    ->useOnImport(fromRaw: static fn(string $raw, $ctx) => $raw)
                    ->showOnExport()
                    ->default('0.00')
                    ->required(),
                Decimal::make('Descuento Línea Corporativa', 'phone_discount')
                    ->useOnImport(fromRaw: static fn(string $raw, $ctx) => $raw)
                    ->showOnExport()
                    ->default('0.00')
                    ->required(),
            ]),
        ];
    }

    /**
     * @param DetailPayroll $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}
