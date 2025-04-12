<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\HumanResources;
use App\Filament\Resources\VacationTypeResource\Pages;
use App\Filament\Resources\VacationTypeResource\RelationManagers;
use App\Models\VacationType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VacationTypeResource extends Resource
{
    protected static ?string $model = VacationType::class;

    protected static ?string $cluster = HumanResources::class;

    protected static ?string $navigationGroup = 'Personal';

    protected static ?string $navigationLabel = 'Tipo de Vacaciones';
    protected static ?string $modelLabel = 'Tipo';

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    //Sort in the cluster
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
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
            'index' => Pages\ManageVacationTypes::route('/'),
        ];
    }
}
