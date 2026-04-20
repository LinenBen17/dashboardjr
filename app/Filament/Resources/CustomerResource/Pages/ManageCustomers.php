<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Filament\Resources\CustomerSpecialRatesResource;
use App\Models\Customer;
use App\Models\CustomerSpecialRates;
use App\Models\Product;
use DefStudio\SearchableInput\Forms\Components\SearchableInput;
use Filament\Actions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRecords;

class ManageCustomers extends ManageRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('tarifas-especiales')
                ->label('Tarifas Especiales')
                ->color('info')
                ->form([
                    Grid::make('Ingreso de Tarifas Especiales')
                        ->columns(3)
                        ->schema([
                            SearchableInput::make('customer_id')
                                ->label('Cliente')
                                ->options(function () {
                                    return Customer::all()
                                        ->mapWithKeys(function ($customer) {
                                            return [
                                                $customer->id => $customer->name . ' - ' . $customer->code,
                                            ];
                                        })
                                        ->toArray();
                                })
                                ->required(),
                            SearchableInput::make('product_id')
                                ->label('Producto')
                                ->options(function () {
                                    return Product::all()
                                        ->pluck('name', 'id')
                                        ->toArray();
                                })
                                ->required(),
                            TextInput::make('rate')
                                ->label('Tarifa')
                                ->required()
                                ->numeric(),
                        ])
                ])
                ->action(function (array $data) {
                    CustomerSpecialRates::create([
                        'customer_id' => $data['customer_id'],
                        'product_id' => $data['product_id'],
                        'special_price' => $data['rate'],
                    ]);
                })
                ->extraModalFooterActions([
                    Actions\Action::make('customer-special-rates')
                        ->label('Ver Tarifas Especiales')
                        ->color('primary')
                        ->url(CustomerSpecialRatesResource::getUrl('index')),
                ]),
        ];
    }
}
