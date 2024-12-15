<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Report;

use App\MoonShine\Controllers\Report;
use App\MoonShine\Resources\AgencyResource;
use App\MoonShine\Resources\DateBenefitResource;
use Illuminate\Support\Facades\Log;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\Card;
use MoonShine\Components\FormBuilder;
use MoonShine\Pages\Crud\IndexPage;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Components\Title;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Collapse;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Divider;
use MoonShine\Decorations\Grid;
use MoonShine\Decorations\LineBreak;
use MoonShine\Decorations\Tab;
use MoonShine\Decorations\Tabs;
use MoonShine\Decorations\TextBlock;
use MoonShine\Fields\Date;
use MoonShine\Fields\DateRange;
use MoonShine\Fields\Field;
use MoonShine\Fields\Hidden;
use MoonShine\Fields\ID;
use MoonShine\Fields\Preview;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Select;
use MoonShine\Fields\Text;
use Throwable;

class ReportIndexPage extends IndexPage
{
    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            ID::make(),
            Text::make('Title'),
        ];
    }

    /**
     * @return list<MoonShineComponent>
     * @throws Throwable
     */
    protected function topLayer(): array
    {
        return [
            Block::make([
                Tabs::make([ 
                    Tab::make('Planilla', [
                        LineBreak::make(),
                        Grid::make([
                            Column::make([
                                Divider::make('Colaboradores Dentro de Planilla')
                                    ->centered(),
                                LineBreak::make(),
                                Collapse::make('Ingresa el Rango de Fecha', [
                                    FormBuilder::make()
                                        ->customAttributes(['target' => '_blank'])
                                        ->action(route('reports.payroll'))
                                        ->method('POST')
                                        ->fields([

                                                Date::make('Del:', 'from'),
                                                Date::make('Al:', 'to'),
                                                Hidden::make('payrrol', 'payroll')
                                                    ->default('DENTRO DE PLANILLA')

                                        ])
                                ])->open()->persist(fn () => false)
                            ])->columnSpan(6),
                            Column::make([
                                Divider::make('Colaboradores Fuera de Planilla')
                                    ->centered(),
                                LineBreak::make(),
                                Collapse::make('Ingresa el Rango de Fecha', [
                                    FormBuilder::make()
                                        ->customAttributes(['target' => '_blank'])
                                        ->action(route('reports.payroll'))
                                        ->method('POST')
                                        ->fields([

                                                Date::make('Del:', 'from'),
                                                Date::make('Al:', 'to'),
                                                Hidden::make('payrrol', 'payroll')
                                                    ->default('FUERA DE PLANILLA')

                                        ])
                                ])->persist(fn () => false)
                            ])->columnSpan(6),
                        ])
                    ]), 
                    Tab::make('Boletas de Pago', [
                        LineBreak::make(),
                        Grid::make([
                            Column::make([
                                Divider::make('Colaboradores Dentro de Planilla')
                                    ->centered(),
                                LineBreak::make(),
                                Collapse::make('Ingresa el Rango de Fecha', [
                                    FormBuilder::make()
                                        ->customAttributes(['target' => '_blank'])
                                        ->action(route('reports.payslips'))
                                        ->method('POST')
                                        ->fields([
                                                Date::make('Del:', 'from'),
                                                Date::make('Al:', 'to'),
                                                Hidden::make('payrrol', 'payroll')
                                                    ->default('DENTRO DE PLANILLA')

                                        ])
                                ])->open()->persist(fn () => false)
                            ])->columnSpan(6),
                            /* Column::make([
                                Divider::make('Colaboradores Fuera de Planilla')
                                    ->centered(),
                                LineBreak::make(),
                                Collapse::make('Ingresa el Rango de Fecha', [
                                    FormBuilder::make()
                                        ->customAttributes(['target' => '_blank'])
                                        ->action(route('reports.payroll'))
                                        ->method('POST')
                                        ->fields([

                                                Date::make('Del:', 'from'),
                                                Date::make('Al:', 'to'),
                                                Hidden::make('payrrol', 'payroll')
                                                    ->default('FUERA DE PLANILLA')

                                        ])
                                ])->persist(fn () => false)
                            ])->columnSpan(6), */
                        ])
                    ]),
                ])
            ])
        ];
    }

    /**
     * @return list<MoonShineComponent>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        return [];
    }

    /**
     * @return list<MoonShineComponent>
     * @throws Throwable
     */
    protected function bottomLayer(): array
    {
        return [];
    }
}
