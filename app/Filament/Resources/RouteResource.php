<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\HumanResources;
use App\Filament\Resources\RouteResource\Pages;
use App\Filament\Resources\RouteResource\RelationManagers;
use App\Models\Route;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class RouteResource extends Resource
{
    protected static ?string $model = Route::class;

    protected static ?string $cluster = HumanResources::class;

    protected static ?string $navigationGroup = 'Logística';

    protected static ?string $navigationLabel = 'Rutas';
    protected static ?string $modelLabel = 'Rutas';

    protected static ?string $navigationIcon = 'heroicon-o-map';

    //Sort in the cluster
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('prefix')
                    ->required()
                    ->maxLength(191)
                    ->unique(Route::class, 'prefix', ignoreRecord: true),
                Forms\Components\Select::make('employee_id')
                    ->required()
                    ->relationship('employees', 'name', function (Builder $query) {
                        // Filter employees(name+last_name) by charge
                        $query->whereHas('charges', function (Builder $query) {
                            $query->where('name', 'Piloto Repartidor');
                        })->select('id', DB::raw("CONCAT(name, ' ', last_name) as name"));
                        // Filter employees(name+last_name) by agency

                        return $query;
                    }),
                Forms\Components\Select::make('agency_id')
                    ->required()
                    ->relationship('agencies', 'name'),
                Forms\Components\TextInput::make('plates')
                    ->required()
                    ->maxLength(191),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('prefix')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee_id')
                    ->numeric()
                    ->getStateUsing(function (Route $record) {
                        return $record->employees->name . ' ' . $record->employees->last_name;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('agency_id')
                    ->numeric()
                    ->getStateUsing(function (Route $record) {
                        return $record->agencies->name;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('plates')
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
            'index' => Pages\ManageRoutes::route('/'),
        ];
    }
}
