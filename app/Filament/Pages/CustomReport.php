<?php

namespace App\Filament\Pages;

use App\Filament\Clusters\HumanResources;
use App\Models\Benefit;
use App\Models\Payroll;
use App\Repositories\PayrollRepository;
use Carbon\Carbon;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Title;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class CustomReport extends Page
{
    protected static string $view = 'filament.pages.custom-report';

    protected static ?string $cluster = HumanResources::class;
    protected static ?string $title = 'Reportes';

    protected static ?string $navigationGroup = 'Planilla';

    protected static ?string $navigationLabel = 'Reportes';
    protected static ?string $modelLabel = 'Reporte';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 14;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Reportes Colaboradores')
                    ->schema([
                        Select::make('report')
                            ->label('Reporte')
                            ->options([
                                'payroll' => 'Planilla',
                                'payslips' => 'Boletas de Pago',
                            ])
                            ->required(),
                        Select::make('payrollState')
                            ->label('Estado de la Planilla')
                            ->options(
                                function () {
                                    $benefits = Payroll::pluck('state', 'id');
                                    return $benefits;
                                }
                            )
                            ->required()
                            ->reactive(),
                        Select::make('benefit')
                            ->label('Beneficio')
                            ->options(
                                function () {
                                    $benefits = Benefit::pluck('name', 'id');
                                    return $benefits;
                                }
                            ),
                        Select::make('employee')
                            ->label('Colaborador')
                            ->options(
                                function (Get $get) {
                                    $employees = DB::table('employees')
                                        ->where('id_payroll', $get('payrollState'))
                                        ->pluck('name', 'id');
                                    return $employees;
                                }
                            )
                    ])
            ])
            ->statePath('data');
    }
}
