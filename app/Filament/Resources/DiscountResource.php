<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\HumanResources;
use App\Filament\Resources\DiscountResource\Pages;
use App\Filament\Resources\DiscountResource\RelationManagers;
use App\Models\Discount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class DiscountResource extends Resource
{
    protected static ?string $model = Discount::class;

    protected static ?string $cluster = HumanResources::class;

    protected static ?string $navigationGroup = 'Planilla';

    protected static ?string $navigationLabel = 'Descuentos';
    protected static ?string $modelLabel = 'Descuento';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-down';

    //Sort in the cluster
    protected static ?int $navigationSort = 14;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->label('Empleado')
                    ->relationship('employees', 'name', fn(Builder $query) => $query->select('id', DB::raw("CONCAT(name, ' ', last_name) as name")))
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options([
                        'anticipo' => 'Anticipo',
                        'ausencia' => 'Ausencia',
                        'otro' => 'Otros',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->default(now())
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->prefix('Q')
                    ->default(0)
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
                Tables\Columns\TextColumn::make('employee_id')
                    ->numeric()
                    ->label('Empleado')
                    ->getStateUsing(function (Discount $record) {
                        return $record->employees->name . ' ' . $record->employees->last_name;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
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
            'index' => Pages\ManageDiscounts::route('/'),
        ];
    }
}
