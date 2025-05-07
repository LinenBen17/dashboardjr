<?php

namespace App\Filament\Resources\DiscountResource\Pages;

use App\Filament\Resources\DiscountResource;
use App\Filament\Resources\LoanResource;
use App\Models\Installments;
use App\Models\Loan;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ManageDiscounts extends ManageRecords
{
    protected static string $resource = DiscountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Préstamos')
                ->color('info')
                ->form([
                    Section::make('Ingreso de Prestamos')
                        ->columns(2)
                        ->schema([
                            Select::make('employee_id')
                                ->label('Empleado')
                                ->relationship('employees', 'name', fn(Builder $query) => $query->select('id', DB::raw("CONCAT(name, ' ', last_name) as name")))
                                ->required(),
                            DatePicker::make('start_date')
                                ->label('Fecha de inicio')
                                ->default(now())
                                ->required(),
                            TextInput::make('amount_loan')
                                ->label('Monto del Préstamo')
                                ->prefix('Q')
                                ->numeric()
                                ->required(),
                            TextInput::make('no_share')
                                ->label('Número de cuotas')
                                ->default(1)
                                ->numeric()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Set $set, ?string $state, Get $get) {
                                    $amount_share = $get('amount_loan') / $state;
                                    $set('amount_share', $amount_share);
                                })
                                ->required(),
                            TextInput::make('amount_share')
                                ->label('Monto en cada cuota')
                                ->prefix('Q')
                                ->default(0)
                                ->disabled()
                                ->dehydrated()
                                ->numeric()
                                ->required(),
                            TextInput::make('comments')
                                ->label('Comentarios')
                                ->maxLength(191)
                                ->required(),
                        ]),
                ])
                ->extraModalFooterActions([
                    Action::make('Visualición')
                        ->url(LoanResource::getUrl('index'))
                        ->openUrlInNewTab(false)
                        ->extraAttributes([
                            'style' => 'background-color: #FF5733; color: #FFFFFF; border-color: #FF5733;'
                        ]),
                ])
                ->action(function (array $data) {
                    $validator = Validator::make($data, [
                        'employee_id' => 'required|exists:employees,id',
                        'start_date' => 'required|date',
                        'amount_loan' => 'required|numeric|min:0',
                        'no_share' => 'required|integer|min:1',
                        'amount_share' => 'required|numeric|min:0',
                        'comments' => 'required|string|max:191',
                    ]);

                    if ($validator->fails()) {
                        Notification::make()
                            ->title('Verifica que todos los campos estén correctamente llenos.')
                            ->body('No puede aplicar a este año.')
                            ->danger()
                            ->persistent()
                            ->send();
                        throw new ValidationException($validator);
                    }

                    try {
                        $loan = Loan::create([
                            'employee_id' => $data['employee_id'],
                            'start_date' => $data['start_date'],
                            'amount_loan' => $data['amount_loan'],
                            'no_share' => $data['no_share'],
                            'amount_share' => $data['amount_share'],
                            'comments' => $data['comments'],
                        ]);

                        $loan_id = $loan->id;

                        // Fecha de ingreso del préstamo
                        $startDate = Carbon::create($data['start_date']);

                        // Iteramos para crear las cuotas
                        for ($i = 1; $i <= $data['no_share']; $i++) {
                            $billingDate = $startDate;

                            if ($billingDate->day <= 15) {
                                Installments::create([
                                    'loan_id' => $loan_id,
                                    'no_installment' => $i,
                                    'amount' => $data['amount_loan'] / $data['no_share'],
                                    'billing_date' => $billingDate->addDays(15 - $billingDate->day),
                                ]);
                            } elseif ($billingDate->day > 15) {
                                Installments::create([
                                    'loan_id' => $loan_id,
                                    'no_installment' => $i,
                                    'amount' => $data['amount_loan'] / $data['no_share'],
                                    'billing_date' => $billingDate->endOfMonth(),
                                ]);
                            }

                            $billingDate->addDays(1);
                        }
                    } catch (\Throwable $th) {
                        Notification::make()
                            ->title('Error al crear el préstamo.')
                            ->body('Verifica que todos los campos estén correctamente llenos.')
                            ->danger()
                            ->persistent()
                            ->send();
                        throw $th;
                    }
                    Notification::make()
                        ->title('Préstamo creado correctamente.')
                        ->body('El préstamo ha sido creado y las cuotas han sido generadas.')
                        ->success()
                        ->send();
                }),

            Actions\CreateAction::make(),
        ];
    }
}
