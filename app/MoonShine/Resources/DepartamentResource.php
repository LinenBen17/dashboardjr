<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Departament;
use Illuminate\Support\Facades\Request;
use MoonShine\Components\Boolean;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Fields\Enum;
use MoonShine\Fields\Number;
use MoonShine\Fields\Switcher;
use MoonShine\Fields\Text;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Handlers\ImportHandler;

/**
 * @extends ModelResource<Departament>
 */
class DepartamentResource extends ModelResource
{
    protected string $model = Departament::class;

    protected string $title = 'Departaments';

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
                    ->showOnExport()
                    ->required(),
                Text::make('Nombre Departamento', 'name')
                    ->useOnImport(fromRaw: static fn(string $raw, $ctx) => $raw)
                    ->sortable()
                    ->showOnExport()
                    ->required(),
                Switcher::make('Status', 'status')
                    ->sortable(),
                Text::make('Prefijo', 'prefix')
                    ->required(),
            ]),
        ];
    }

    /**
     * @param Departament $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}
