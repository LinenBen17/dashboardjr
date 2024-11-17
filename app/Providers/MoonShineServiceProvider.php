<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Agency;
use App\Models\Charge;
use App\Models\DetailPayroll;
use App\Models\Payroll;
use App\MoonShine\Resources\AgencyResource;
use App\MoonShine\Resources\BenefitResource;
use App\MoonShine\Resources\BonusResource;
use App\MoonShine\Resources\ChargeResource;
use App\MoonShine\Resources\CivilStatusResource;
use App\MoonShine\Resources\DateBenefitResource;
use App\MoonShine\Resources\DepartamentResource;
use App\MoonShine\Resources\DetailPayrollResource;
use App\MoonShine\Resources\DiscountResource;
use App\MoonShine\Resources\DistrictResource;
use App\MoonShine\Resources\EmployeeResource;
use App\MoonShine\Resources\GenderResource;
use App\MoonShine\Resources\PayrollResource;
use App\MoonShine\Resources\PostResource;
use App\MoonShine\Resources\ReportResource;
use App\MoonShine\Resources\TownResource;
use MoonShine\Providers\MoonShineApplicationServiceProvider;
use MoonShine\MoonShine;
use MoonShine\Menu\MenuGroup;
use MoonShine\Menu\MenuItem;
use MoonShine\Resources\MoonShineUserResource;
use MoonShine\Resources\MoonShineUserRoleResource;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Menu\MenuElement;
use MoonShine\Pages\Page;
use Closure;
use MoonShine\Decorations\Block;
use MoonShine\Menu\MenuDivider;

class MoonShineServiceProvider extends MoonShineApplicationServiceProvider
{
    /**
     * @return list<ResourceContract>
     */
    protected function resources(): array
    {
        return [];
    }

    /**
     * @return list<Page>
     */
    protected function pages(): array
    {
        return [];
    }
    
    /**
     * @return Closure|list<MenuElement>
     */
    protected function menu(): array
    {
        return [
            MenuGroup::make(static fn() => __('moonshine::ui.resource.system'), [
                MenuItem::make(
                    static fn() => __('moonshine::ui.resource.admins_title'),
                    new MoonShineUserResource()
                ),
                MenuItem::make(
                    static fn() => __('moonshine::ui.resource.role_title'),
                    new MoonShineUserRoleResource()
                ),
            ]),

            MenuItem::make('Post Menú', new PostResource),
            MenuGroup::make('Recursos Humanos', [
                MenuGroup::make('Logística', [
                    MenuItem::make('Departamentos', new DepartamentResource),
                    MenuItem::make('Agencias', new AgencyResource),
                    MenuItem::make('Municipios', new TownResource),
                ]),
                MenuDivider::make(),
                MenuGroup::make('Personal', [
                    MenuItem::make('Empleados', new EmployeeResource),
                    MenuItem::make('Cargos Empleados', new ChargeResource),
                    MenuItem::make('Estado Civil', new CivilStatusResource),
                    MenuItem::make('Género', new GenderResource),
                    MenuItem::make('Estados Planilla', new PayrollResource),
                    MenuItem::make('Circunscripciones', new DistrictResource),
                ]),
                MenuDivider::make(), 
                MenuGroup::make('Planilla', [
                    MenuItem::make('Beneficios', new BenefitResource),
                    MenuItem::make('Detalle', new DetailPayrollResource),
                    MenuItem::make('Bonos', new BonusResource),
                    MenuItem::make('Descuentos', new DiscountResource),
                    MenuItem::make('Reportes', new ReportResource),
                ])
            ]),
        ];
    }

    /**
     * @return Closure|array{css: string, colors: array, darkColors: array}
     */
    protected function theme(): array
    {
        return [];
    }

    public function boot(): void
    {
        parent::boot();
 
        moonShineAssets()->add([
            'assets/main.js',
        ]); 
    }
}
