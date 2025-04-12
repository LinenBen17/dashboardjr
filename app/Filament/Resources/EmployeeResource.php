<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\HumanResources;
use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Log\Logger;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $cluster = HumanResources::class;

    protected static ?string $navigationGroup = 'Personal';

    protected static ?string $navigationLabel = 'Empleados';
    protected static ?string $modelLabel = 'Empleados';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    //Sort in the cluster
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Datos del Empleado')
                ->schema([
                    // View::make('filament.resources.employee-script'),
                    Grid::make(6)
                        ->schema([
                            TextInput::make('name')
                                ->label('Nombres')
                                ->required()
                                ->columnSpan(3),

                            TextInput::make('last_name')
                                ->label('Apellidos')
                                ->required()
                                ->columnSpan(3),

                            Select::make('gender_id')
                                ->label('Género')
                                ->relationship(name: 'genders', titleAttribute: 'name')
                                ->required()
                                ->columnSpan(3),

                            DatePicker::make('birth_date')
                                ->label('Fecha de nacimiento')
                                ->required()
                                ->columnSpan(3),

                            Select::make('civil_status_id')
                                ->label('Estado Civil')
                                ->relationship(name: 'civilStatus', titleAttribute: 'name')
                                ->required()
                                ->columnSpan(3),

                            TextInput::make('age')
                                ->label('Edad')
                                ->numeric()
                                ->required()
                                ->columnSpan(3),
                        ]),

                    Grid::make(6)
                        ->schema([
                            TextInput::make('address')
                                ->label('Dirección Domiciliaria')
                                ->required()
                                ->columnSpan(4),

                            Select::make('departament_id')
                                ->label('Departamento')
                                ->relationship(name: 'departaments', titleAttribute: 'name')
                                ->required()
                                ->reactive()
                                ->columnSpan(2),

                            Select::make('town_id')
                                ->label('Municipio')
                                ->relationship('towns', 'name', fn($query, $get) =>
                                $query->where('agency_id', $get('departament_id')))
                                ->required()
                                ->columnSpan(3),

                            TextInput::make('zone')
                                ->label('Zona')
                                ->required()
                                ->columnSpan(1),

                            TextInput::make('birthplace')
                                ->label('Lugar de Nacimiento')
                                ->required()
                                ->reactive()
                                ->columnSpan(3),

                            TextInput::make('children')
                                ->label('Hijos')
                                ->numeric()
                                ->required()
                                ->columnSpan(2),
                        ]),

                    Grid::make(6)
                        ->schema([
                            Select::make('nationality_id')
                                ->label('Nacionalidad')
                                ->required()
                                ->relationship(name: 'nationalities', titleAttribute: 'name')
                                ->columnSpan(3),

                            TextInput::make('phone')
                                ->label('Teléfono')
                                ->tel()
                                ->required()
                                ->columnSpan(1),

                            TextInput::make('cellphone')
                                ->label('Celular')
                                ->tel()
                                ->required()
                                ->columnSpan(2),

                            TextInput::make('dpi')
                                ->label('DPI')
                                ->numeric()
                                ->required()
                                ->columnSpan(3),

                            TextInput::make('nit')
                                ->label('NIT')
                                ->numeric()
                                ->required()
                                ->columnSpan(3),

                            TextInput::make('email')
                                ->label('Correo Electrónico')
                                ->email()
                                ->required()
                                ->columnSpan(6),
                        ]),
                ]),

            Section::make('Datos de Contratación')
                ->schema([
                    Grid::make(6)
                        ->schema([
                            DatePicker::make('entry_date')
                                ->label('Fecha Ingreso')
                                ->required()
                                ->columnSpan(2),

                            Select::make('id_agency')
                                ->label('Agencia')
                                ->relationship(name: 'agencies', titleAttribute: 'name')
                                ->required()
                                ->columnSpan(2),

                            Select::make('id_charge')
                                ->label('Cargo')
                                ->relationship(name: 'charges', titleAttribute: 'name')
                                ->required()
                                ->columnSpan(2),

                            Select::make('id_payroll')
                                ->label('Estado Planilla')
                                ->relationship(name: 'payrolls', titleAttribute: 'state')
                                ->required()
                                ->columnSpan(3),

                            TextInput::make('bank_account')
                                ->label('Cuenta Bancaria')
                                ->numeric()
                                ->required()
                                ->columnSpan(3),
                        ]),
                ]),

            Grid::make(2)
                ->schema([
                    FileUpload::make('photo')
                        ->label('Fotografía')
                        ->disk('public')
                        ->image()
                        ->imagePreviewHeight('150')
                        ->required(),

                    RichEditor::make('comments')
                        ->id('comments')
                        ->label('Observaciones')
                        ->maxLength(500)
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nombre completo')
                    ->getStateUsing(function ($record) {
                        return "{$record->name} {$record->last_name}";
                    })
                    ->searchable(['name', 'last_name']),
                Tables\Columns\TextColumn::make('entry_date')
                    ->date()

                    ->sortable(),
                Tables\Columns\TextColumn::make('agencies.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('charges.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payrolls.state')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bank_account')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('towns.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('departaments.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('zone')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('birthplace')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nationalities.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cellphone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('age')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('civilStatus.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('genders.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('children')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dpi')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nit')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('photo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('comments')
                    ->limit(10)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
