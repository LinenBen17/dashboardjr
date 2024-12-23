<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Report;
use App\MoonShine\Pages\Report\ReportIndexPage;
use App\MoonShine\Pages\Report\ReportFormPage;
use App\MoonShine\Pages\Report\ReportDetailPage;
use MoonShine\Enums\PageType;
use MoonShine\Resources\ModelResource;
use MoonShine\Pages\Page;

/**
 * @extends ModelResource<Report>
 */
class ReportResource extends ModelResource
{
    protected string $model = Report::class;

    protected string $title = 'Creación de Reportes';

    protected ?PageType $pageType = PageType::FORM;

    protected array $assets = [
        'assets/js/reportResource.js',
    ];

    /**
     * @return list<Page>
     */
    public function pages(): array
    {
        return [
            
            ReportIndexPage::make($this->title()),
            ReportFormPage::make(
                $this->getItemID()
                    ? __('moonshine::ui.edit')
                    : __('moonshine::ui.add')
            ),
            ReportDetailPage::make(__('moonshine::ui.show')),
        ];
    }

    /**
     * @param Report $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}
