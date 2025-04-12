<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\HumanResources;
use App\Filament\Resources\VacationResource\Pages;
use App\Filament\Resources\VacationResource\RelationManagers;
use App\Models\Vacation;
use App\Models\VacationHistory;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\Exports\Exporter;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class VacationResource extends Resource
{
    protected static ?string $model = Vacation::class;

    protected static ?string $cluster = HumanResources::class;

    protected static ?string $navigationGroup = 'Personal';

    protected static ?string $navigationLabel = 'Vacaciones';
    protected static ?string $modelLabel = 'Vacacion';

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    //Sort in the cluster
    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('employee_id')
                    ->label('Empleado')
                    ->relationship('employees', 'name', fn(Builder $query) => $query->select('id', DB::raw("CONCAT(name, ' ', last_name) as name")))
                    ->required(),
                TextInput::make('request_year')
                    ->label('Año de solicitud')
                    ->required()
                    ->numeric(),
                DatePicker::make('request_date')
                    ->label('Fecha de solicitud')
                    ->default(now())
                    ->required(),
                DatePicker::make('start_date')
                    ->label('Fecha de inicio')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('Fecha de fin')
                    ->required(),
                TextInput::make('days_requested')
                    ->label('Días solicitados')
                    ->required()
                    ->numeric(),
                Select::make('vacation_type_id')
                    ->label('Tipo de vacaciones')
                    ->relationship('vacationType', 'name')
                    ->required(),
                Textarea::make('comments')
                    ->label('Comentarios')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_id')
                    ->label('Empleado')
                    //Mostrar nombre y apellido
                    ->getStateUsing(function (Vacation $record) {
                        return $record->employees->name . ' ' . $record->employees->last_name;
                    })
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('request_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('days_requested')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vacationType.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vacationHistory.year')
                    ->label('Periodo')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('comments')
                    ->searchable(),
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
                Tables\Actions\DeleteAction::make()
                    ->action(function ($record) {
                        $vacation = Vacation::find($record->id);
                        $vacation_history = VacationHistory::find($vacation->vacation_history_id);

                        // Restaurando los días restantes
                        $vacation_history->days_used = $vacation_history->days_used - $vacation->days_requested;
                        $vacation_history->days_remaining = $vacation_history->days_remaining + $vacation->days_requested;

                        $vacation_history->save();
                        $vacation->delete();
                    }),
                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-text')
                    ->url(fn($record) => route('vacation_format', $record->id))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageVacations::route('/'),
        ];
    }
}
