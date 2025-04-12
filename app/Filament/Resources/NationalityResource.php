<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\HumanResources;
use App\Filament\Resources\NationalityResource\Pages;
use App\Filament\Resources\NationalityResource\RelationManagers;
use App\Models\Nationality;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NationalityResource extends Resource
{
    protected static ?string $model = Nationality::class;

    protected static ?string $cluster = HumanResources::class;

    protected static ?string $navigationGroup = 'Logística';

    protected static ?string $navigationLabel = 'Nacionalidades';
    protected static ?string $modelLabel = 'Nacionalidad';

    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $label = 'Nacionalidad';

    //Sort in the cluster
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('code')
                    ->label('Código')
                    ->numeric()
                    ->prefix('+')
                    ->maxLength(5),
                FileUpload::make('flag')
                    ->label('Bandera')
                    ->image()
                    ->disk('public')
                    ->directory('nationalities')
                    ->preserveFilenames()
                    ->required()
                    ->maxSize(1024)
                    ->acceptedFileTypes(['image/*'])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('flag')
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
            'index' => Pages\ManageNationalities::route('/'),
        ];
    }
}
