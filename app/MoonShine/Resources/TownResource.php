<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Departament;
use Illuminate\Database\Eloquent\Model;
use App\Models\Town;
use Illuminate\Support\Facades\Request;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Relationships\BelongsToMany;
use MoonShine\Fields\Switcher;
use MoonShine\Fields\Text;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Handlers\ImportHandler;
use Sweet1s\MoonshineRBAC\Traits\WithRolePermissions;

/**
 * @extends ModelResource<Town>
 */
class TownResource extends ModelResource
{
    use WithRolePermissions;

    protected string $model = Town::class;

    protected string $title = 'Towns';

    protected bool $createInModal = true;
    protected bool $editInModal = true;
    protected bool $detailInModal = true;

    protected int $itemsPerPage = 10;

    public function redirectAfterSave(): string
    {
        $referer = Request::header('referer');
        return $referer ?: '/';
    }
    public function import(): ?ImportHandler
    {
        return ImportHandler::make('Importar');
    }

    public function export(): ?ExportHandler
    {
        return ExportHandler::make('Exportar');
    }
    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            Block::make([
                ID::make()->sortable()
                    ->useOnImport()
                    ->showOnExport(),
                Text::make('Nombre del Municipio', 'name')->sortable()
                    ->useOnImport(fromRaw: static fn(string $raw, $ctx) => $raw)
                    ->showOnExport()
                    ->sortable(),
                BelongsTo::make('Departamento del Municipio', 'departaments', 'name')
                    ->useOnImport(fromRaw: static fn(string $raw, $ctx) => $raw)
                    ->showOnExport()
                    ->sortable()
                    ->reactive(function (Fields $fields, ?string $value, Field $field) {
                        // Obtener el departamento seleccionado por su ID
                        $departament = Departament::find($value);

                        // Si se encuentra el departamento, establecer su 'prefix' en el campo correspondiente
                        if ($departament) {
                            $fields->findByColumn('prefix')
                                ?->setValue($departament->prefix);

                            $fields->findByColumn('status')
                                ?->setValue($departament->status);
                        }

                        return $fields;
                    }),
                Text::make('Prefijo', 'prefix')
                    ->useOnImport(fromRaw: static fn(string $raw, $ctx) => $raw)
                    ->showOnExport()
                    ->sortable()
                    ->readonly()
                    ->reactive(),
                Switcher::make('Status', 'status')
                    ->sortable(),
            ]),
        ];
    }

    /**
     * @param Town $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}
