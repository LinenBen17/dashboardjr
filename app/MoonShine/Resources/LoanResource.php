<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Loan;
use ForestLynx\MoonShine\Fields\Decimal;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\Layout\Flash;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Fields\Date;
use MoonShine\Fields\Number;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Fields\Textarea;
use Sweet1s\MoonshineRBAC\Traits\WithRolePermissions;

/**
 * @extends ModelResource<Loan>
 */
class LoanResource extends ModelResource
{
    use WithRolePermissions;

    protected string $model = Loan::class;

    protected string $title = 'Loans';

    protected bool $createInModal = true;
    protected bool $editInModal = true;
    protected bool $detailInModal = true;

    protected bool $withPolicy = false;

    protected int $itemsPerPage = 10;

    public function getActiveActions(): array
    {
        return ['view'];
    }

    public function indexButtons(): array
    {
        return [
            ActionButton::make(
                'PDF',
                fn(Model $item) => route('loans.loan_format', $item->getKey())
            )
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
    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            Block::make([
                ID::make('No. Préstamo', 'id')->sortable(),
                BelongsTo::make(
                    'Empleado',
                    'employees',
                    fn($item) => "$item->name $item->last_name"
                )->searchable()
                    ->nullable()
                    ->required(),
                Date::make('Fecha de inicio', 'start_date')->required(),
                Decimal::make('Monto del Préstamo', 'amount_loan')->required(),
                Number::make('Número de cuotas', 'no_share')->required(),
                Decimal::make('Monto en cada cuota', 'amount_share')->required(),
                Textarea::make('Descripición', 'comments')->required(),
                HasMany::make(
                    'Cuotas',
                    'installments',
                    resource: new InstallmentsResource(),
                )->onlyLink()
            ]),
        ];
    }

    /**
     * @param Loan $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function metrics(): array
    {
        return [
            Flash::make(key: 'success', type: 'success', withToast: true, removable: true),
            Flash::make(key: 'fail', type: 'error', withToast: true, removable: false),
        ];
    }
    public function rules(Model $item): array
    {
        return [];
    }
}
