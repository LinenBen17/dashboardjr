<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Shipping;
use App\Filament\Imports\CustomerImporter;
use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $cluster = Shipping::class;

    protected static ?string $navigationLabel = 'Clientes';
    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $label = 'Cliente';

    //Sort in the cluster
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(191),
                Forms\Components\TextInput::make('contact_name')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('contact_phone')
                    ->required(),
                Forms\Components\Select::make('payment_method_id')
                    ->required()
                    ->default(1)
                    ->relationship(name: 'paymentMethods', titleAttribute: 'name'),
                Forms\Components\Select::make('departament_id')
                    ->required()
                    ->reactive()
                    ->relationship(name: 'departaments', titleAttribute: 'name'),
                Forms\Components\Select::make('town_id')
                    ->label('Municipio')
                    ->relationship('towns', 'name', fn($query, $get) =>
                    $query->where('agency_id', $get('departament_id')))
                    ->required(),
                Forms\Components\TextInput::make('prefix_origin')
                    ->required()
                    ->maxLength(10),
                Forms\Components\Select::make('employee_id')
                    ->label('Vendedor')
                    ->required()
                    ->options(function () {
                        $employee = DB::table('employees')
                            ->leftJoin('charges', 'employees.id_charge', '=', 'charges.id')
                            ->select('charges.name', 'employees.id', DB::raw("CONCAT(employees.name, ' ', employees.last_name) AS name"))
                            ->where('charges.name', 'LIKE', '%Vendedor%')
                            ->pluck('name', 'id');
                        return $employee;
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ImportAction::make()
                    ->importer(CustomerImporter::class)
            ])
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_phone')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('departament_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('town_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('prefix_origin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('employee_id')
                    ->numeric()
                    ->sortable(),
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCustomers::route('/'),
        ];
    }
}
