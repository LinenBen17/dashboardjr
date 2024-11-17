<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\CivilStatus;
use Illuminate\Support\Facades\Request;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Fields\Text;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Handlers\ImportHandler;

/**
 * @extends ModelResource<CivilStatus>
 */
class CivilStatusResource extends ModelResource
{
    protected string $model = CivilStatus::class;

    protected string $title = 'civil_statuses';

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

    public function import(): ?ImportHandler
    {
        return null;
    } 

    public function export(): ?ExportHandler 
    {
        return null;
    } 
    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            Block::make([
                ID::make()->sortable(),
                Text::make('Estado Civil', 'name')
                    ->required(),
            ]),
        ];
    }

    /**
     * @param CivilStatus $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}
