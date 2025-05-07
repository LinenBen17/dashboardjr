<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Filament\Resources\LoanResource\RelationManagers;
use App\Models\Installments;
use App\Models\Loan;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    //Ocultar del panel 
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->label('Empleado')
                    ->relationship('employees', 'name', fn(Builder $query) => $query->select('id', DB::raw("CONCAT(name, ' ', last_name) as name")))
                    ->required(),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\TextInput::make('amount_loan')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('no_share')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('amount_share')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('comments')
                    ->required()
                    ->maxLength(191),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('No. Préstamo')
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee_id')
                    ->numeric()
                    ->label('Empleado')
                    ->getStateUsing(function (Loan $record) {
                        return $record->employees->name . ' ' . $record->employees->last_name;
                    }),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_loan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_share')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('comments')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Acción personalizada para ver cuotas en un modal
                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-text')
                    ->url(fn($record) => route('loan_format', $record->id))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('view_installments')
                    ->label('Cuotas')
                    ->icon('heroicon-o-eye')
                    ->badge(fn(Loan $record): string => $record->installments->count())
                    ->modalHeading(fn($record) => 'Cuotas del Préstamo #' . $record->id)
                    ->modalContent(function ($record) {
                        // Obtener las cuotas asociadas al préstamo
                        $installments = $record->installments;

                        // Generar una tabla HTML simple para mostrar las cuotas
                        $html = '<div class="overflow-x-auto"><table class="w-full text-left border-collapse">';
                        $html .= '<thead><tr>';
                        $html .= '<th class="p-2 border">No. Cuota</th>';
                        $html .= '<th class="p-2 border">Monto</th>';
                        $html .= '<th class="p-2 border">Estado</th>';
                        $html .= '<th class="p-2 border">Fecha de vencimiento</th>';
                        $html .= '</tr></thead><tbody>';

                        if ($installments->isEmpty()) {
                            $html .= '<tr><td colspan="3" class="p-2 border text-center">No hay cuotas asociadas</td></tr>';
                        } else {
                            foreach ($installments as $installment) {
                                $html .= '<tr>';
                                $html .= '<td class="p-2 border">' . $installment->no_installment . '</td>';
                                $html .= '<td class="p-2 border">Q.' . number_format($installment->amount, 2) . '</td>';
                                $html .= '<td class="p-2 border">';
                                $html .= '<span class="flex w-3 h-3 me-3 rounded-full" style="margin:auto;background-color:' . ($installment->status ? '#10b981' : '#ef4444') . '"></span>';
                                $html .= '</td>';
                                $html .= '<td class="p-2 border">' . Carbon::parse($installment->billing_date)->format('d/m/y') . '</td>';
                                $html .= '</tr>';
                            }
                        }

                        $html .= '</tbody></table></div>';

                        return new HtmlString($html);
                    })
                    ->modalSubmitAction(false) // Sin botón de "Enviar"
                    ->modalCancelActionLabel('Cerrar')
                    // ->button()
                    ->color('primary'),

                // Acción personalizada para eliminar el préstamo
                Tables\Actions\DeleteAction::make()
                    ->action(function ($record) {
                        try {
                            $loan = Loan::find($record->id);

                            if ($loan) {
                                // Eliminar las cuotas asociadas
                                Installments::where('loan_id', $loan->id)->delete();

                                // Eliminar el préstamo
                                $loan->delete();

                                Notification::make()
                                    ->title('Préstamo eliminado con éxito.')
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Préstamo no encontrado.')
                                    ->success()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error al eliminar el préstamo.')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalDescription('Esto eliminará el préstamo y todo lo relacionado con el mismo. ¿Estás seguro?')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLoans::route('/'),
        ];
    }
}
