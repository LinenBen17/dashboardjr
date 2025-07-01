<?php

namespace App\Filament\Resources\ShipmentEntryResource\Pages;

use App\Filament\Resources\ShipmentEntryResource;
use App\Models\ShipmentEntry;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\DB;
use Mockery\Matcher\Not;

class CreateShipmentEntry extends CreateRecord
{
    protected static string $resource = ShipmentEntryResource::class;

    public array $childGuides = [];
    public int $totalPieces = 0;

    public function addChildGuide(string $guide)
    {
        $guide = trim($guide);

        if (!$guide) return;

        if (in_array($guide, $this->childGuides)) {
            return;
        }
        if (count($this->childGuides) >= ($this->totalPieces - 1)) {
            Notification::make()
                ->title('Máximo de guías hijas alcanzado')
                ->body('No se pueden agregar más de ' . $this->totalPieces - 1 . ' guías hijas.')
                ->warning()
                ->send();
            return;
        }
        $this->childGuides[] = $guide;
    }

    protected function getFormActions(): array
    {
        return [
            // Botón visible
            Action::make('trySave')
                ->label('Guardar')
                ->form([
                    Grid::make()
                        ->columns(2)
                        ->schema([
                            TextInput::make('child')
                                ->label('Guia Hija')
                                ->id('child')
                                ->placeholder('Escanee las guías hijas')
                                ->helperText('Estas guías hijas estarán enlazadas al envío.')
                                ->extraAlpineAttributes([
                                    'x-on:keydown' => 'window.addGuide(event)',
                                ])
                                ->maxLength(50),
                            Placeholder::make('countChilds')
                                ->content(fn($livewire) => count($livewire->childGuides))
                                ->label('Guías Hijas Enlazadas')
                                ->extraAttributes([
                                    'style' => 'font-size: 24pt;',
                                ]),
                            Toggle::make('linkLater')
                                ->label('Enlazar Después')
                                ->onColor('success')
                        ]),
                ])
                ->modalHeading('Enlace de Guías Hijas')
                ->modalHidden(
                    function (): bool {
                        $data = $this->form->getState();
                        $this->totalPieces = 0;

                        // Get total pieces from products
                        foreach ($data['products'] as $product => $value) {
                            if (isset($value['pieces'])) {
                                $this->totalPieces += $value['pieces'];
                            }
                        }

                        if ($this->totalPieces > 1) {
                            return false;
                        } else {
                            return true;
                        }
                    }
                )
                ->action(function (array $data): void {
                    if ($this->totalPieces == 1) {
                        $this->handleSave(true);
                    }
                    if ($data['linkLater']) {
                        $this->handleSave(true);
                        $this->childGuides = [];
                        return;
                    }
                    if (count($this->childGuides) < ($this->totalPieces - 1)) {
                        Notification::make()
                            ->title('Guías hijas incompletas')
                            ->body('Es necesario enlazar ' . ($this->totalPieces - 1) . ' guías hijas. ' . count($this->childGuides) . ' guías hijas actualmente enlazadas.')
                            ->warning()
                            ->send();
                        $this->childGuides = [];
                        return;
                    }

                    return;
                })
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $arrayProductDescription = [];

        //Establecer codigo en caso de que sea solo 1 pieza
        if ($this->totalPieces == 1) {
            foreach ($data['products'] as $product => $value) {
                if (isset($value['product_id'])) {
                    $data['product_id'] = $value['product_id'];
                }
            }
        } else {
            $data['product_id'] = null;
        }

        // Establecer descripcion completa del pedido si tuviese más de 1 pieza o no
        foreach ($data['products'] as $product => $value) {
            if (isset($value['product_description'])) {
                $arrayProductDescription[] = $value['product_description'];
            }
        }

        $data['product_description'] = implode(',', array_unique($arrayProductDescription));
        $data['pieces'] = $this->totalPieces;

        return $data;
    }

    public function handleSave($linkGuidesLater): void
    {
        $data = $this->form->getState();

        // Establecer descripcion completa del pedido si tuviese más de 1 pieza o no
        $arrayProductDescription = [];

        foreach ($data['products'] as $product => $value) {
            if (isset($value['product_description'])) {
                $arrayProductDescription[] = $value['product_description'];
            }
        }

        $productDescription = implode(',', array_unique($arrayProductDescription)) ?? null;
        $data['product_description'] = $productDescription;

        //Establecer codigo en caso de que sea solo 1 pieza
        $productId = 0;
        if ($this->totalPieces == 1) {
            foreach ($data['products'] as $product => $value) {
                if (isset($value['product_id'])) {
                    $productId = $value['product_id'];
                    $data['product_id'] = $value['product_id'];
                }
            }
        } else {
            $productId = null;
            $data['product_id'] = null;
        }

        //obtener la forma de pago
        $data['payment_method'] = DB::table('payment_methods')
            ->where('id', $data['payment_method_id'])
            ->value('name');

        //Añadir total piezas al array
        $data['total_pieces'] = $this->totalPieces;
        $data['style'] = asset('css/print-mother.css');

        try {
            if ($linkGuidesLater) {
                $this->record = ShipmentEntry::create([
                    'mother' => $data['mother'],
                    'sender_code' => $data['sender_code'] ?? null,
                    'sender_name' => $data['sender_name'],
                    'sender_address' => $data['sender_address'],
                    'sender_phone' => $data['sender_phone'],
                    'receiver_code' => $data['receiver_code'] ?? null,
                    'receiver_name' => $data['receiver_name'],
                    'receiver_address' => $data['receiver_address'],
                    'receiver_phone' => $data['receiver_phone'],
                    'prefix_origin' => $data['prefix_origin'],
                    'prefix_destination' => $data['prefix_destination'],
                    'town_id' => $data['town_id'],
                    'product_id' => $productId,
                    'product_description' => $productDescription,
                    'pieces' => $this->totalPieces,
                    'unit_price' => ($this->totalPieces == 1) ? $data['total'] : 0,
                    'sender_total' => $data['sender_total'] ?? 0,
                    'receiver_total' => $data['receiver_total'] ?? 0,
                    'total' => $data['total'],
                    'date_guide' => $data['date_guide'],
                    'payment_method_id' => $data['payment_method_id'],
                    'no_manifest' => $data['no_manifest'] ?? null,
                ]);
            }
            Notification::make()
                ->title('Envío creado exitosamente')
                ->success()
                ->send();

            //Limpia arrays internos
            $this->childGuides = [];
            $this->totalPieces = 0;

            //Calcula el siguiente número de guía madre
            $nextMother = ((int) $data['mother']) + 1;

            //Actualiza el numero de madre
            $user       = Filament::auth()->user();
            $custom     = $user->custom_fields ?? [];
            $custom['serial_number'] = $nextMother;

            $user->custom_fields = $custom;
            $user->save();

            //Resetea todo y vuelve a llenar sólo mother
            $this->form->fill();
            $this->form->fill([
                'mother'     => $nextMother,
                'date_guide' => now(),
                'products'   => [
                    [            // ← primer (y único) ítem vacío
                        'product_id'          => null,
                        'product_description' => null,
                        'pieces'              => null,
                    ],
                ],
            ]);
            $this->dispatch('print-guide', $data);
            $this->dispatch('focus-codeSender');
        } catch (\Throwable $th) {
            Notification::make()
                ->title('Hubo un error al guardar')
                ->warning()
                ->send();
            exit();
        }
    }
}
