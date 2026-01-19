{{-- MODAL MODIFICACION DE GUÍAS --}}
<x-filament::modal :id="$id ?? 'updateGuides'" width="4xl">
    <x-slot name="heading">
        {{ $heading ?? 'Modificación de Guías' }}
    </x-slot>

    {{-- COLUMNA IZQUIERDA --}}
    <x-filament::grid.column style="--col-span-default: span 1 / span 1;">
        {{-- Primer Contenedor / Información por defecto --}}
        <x-filament::section>
            <div class="flex gap-4 justify-between">
                <div class="mb-4">
                    <label for="search_guide_update"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        No. Guía
                        <span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>

                    <div class="flex gap-2 items-center">
                        <x-filament::input.wrapper class="flex-1">
                            <x-filament::input type="text" placeholder="Guía Madre: 1234567" name="guide"
                                id="search_guide_update" />
                        </x-filament::input.wrapper>

                        <x-filament::button id="searchGuideBtnUpdate" color="primary">
                            Buscar
                        </x-filament::button>
                    </div>
                </div>

                <div>
                    <label for="date_guide_update"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Fecha<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="date" wire:model="date_guide_update" name="date_guide_update"
                            id="date_guide_update" />
                    </x-filament::input.wrapper>
                </div>
                <div>
                    <label for="payment_method_id_update"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Forma de Pago<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model="payment_method_id_update" name="payment_method_id_update"
                            id="payment_method_id_update"
                            class="fi-fo-field-input block w-full rounded-lg border-gray-300">
                            <option value="">Seleccione una opción</option>
                            @foreach ($paymentMethods as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
                <div>
                    <label for="manifest_code_update"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Codigo de Manifiesto<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" wire:model="manifest_code_update" name="manifest_code_update"
                            id="manifest_code_update" />
                    </x-filament::input.wrapper>
                </div>
            </div>
        </x-filament::section>
        <br>
        <x-filament::section collapsible collapsed>
            <x-slot name="heading">
                Datos de Remitente y Destinatario
            </x-slot>
            {{-- 4 columnas remitente --}}
            <div class="flex gap-4 justify-between">
                <div>
                    <label for="codigo_remitente_update"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Codigo Remitente<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="number" wire:model="sender_code_update" name="sender_code_update"
                            id="codigo_remitente_update" />
                    </x-filament::input.wrapper>
                </div>
                <div>
                    <label for="sender_name_update"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Nombre Remitente<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" wire:model="sender_name_update" name="sender_name_update"
                            id="sender_name_update" />
                    </x-filament::input.wrapper>
                </div>
                <div>
                    <label for="sender_address_update"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Dirección Remitente<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" wire:model="sender_address_update"
                            name="sender_address_update" id="sender_address_update" />
                    </x-filament::input.wrapper>
                </div>
                <div>
                    <label for="sender_phone_update"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Teléfono Remitente<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" wire:model="sender_phone_update" name="sender_phone_update"
                            id="sender_phone_update" />
                    </x-filament::input.wrapper>
                </div>
            </div>
            <br>
            {{-- 4 columnas destinatario --}}
            <div class="flex gap-4 justify-between">
                <div>
                    <label for="codigo_destinatario_update"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Codigo Destinatario<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="number" wire:model="receiver_code_update" name="receiver_code_update"
                            id="codigo_destinatario_update" />
                    </x-filament::input.wrapper>
                </div>
                <div>
                    <label for="receiver_name_update"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Nombre Destinatario<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" wire:model="receiver_name_update" name="receiver_name_update"
                            id="receiver_name_update" />
                    </x-filament::input.wrapper>
                </div>
                <div>
                    <label for="receiver_address_update"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Dirección Destinatario<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" wire:model="receiver_address_update"
                            name="receiver_address_update" id="receiver_address_update" />
                    </x-filament::input.wrapper>
                </div>
                <div>
                    <label for="receiver_phone_update"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Teléfono Destinatario<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" wire:model="receiver_phone_update"
                            name="receiver_phone_update" id="receiver_phone_update" />
                    </x-filament::input.wrapper>
                </div>
            </div>
        </x-filament::section>
        <br>
        <x-filament::section collapsible collapsed>
            <x-slot name="heading">
                Datos de Origen, Destino y Ruta
            </x-slot>
            <div class="flex gap-4 justify-between">
                <div>
                    <label for="prefix_origen_update"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Origen<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" wire:model="prefix_origin_update"
                            name="prefix_origin_update" id="prefix_origen_update" value="" />
                    </x-filament::input.wrapper>
                </div>
                <br>
                <div>
                    <label for="prefix_destino_update"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Destino<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" wire:model="prefix_destination_update"
                            name="prefix_destination_update" id="prefix_destino_update" />
                    </x-filament::input.wrapper>
                </div>
                <br>
                <div>
                    <label for="town_id_update"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Municipio<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:ignore name="town_id_update" id="town_id_update">
                            <option value="">Seleccione una opción</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
                <div>
                    <label for="route_destino_update"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Ruta<span class="text-danger-600 dark:text-danger-400 font-medium"></span>
                    </label>
                    <x-filament::input.wrapper disabled>
                        <x-filament::input type="text" wire:model="route_destination_update"
                            name="route_destination_update" id="route_destino_update" disabled readonly />
                    </x-filament::input.wrapper>
                </div>
            </div>
        </x-filament::section>
        <br>
        <x-filament::section collapsible collapsed>
            <x-slot name="heading">
                Detalle de Productos
            </x-slot>
            <label for=""
                class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                Productos
            </label>
            {{-- change bg section --}}
            @foreach ($productos as $index => $producto)
                @if ($index > 0)
                    <br>
                @endif
                <x-filament::section class="bg-gray-100 dark:bg-gray-800 rounded-lg">
                    @if ($index > 0)
                        <div class="flex items-end">
                            <x-slot name="headerEnd">
                                <x-filament::icon-button icon="heroicon-m-trash"
                                    wire:click="removeProduct({{ $index }})" label="" color="danger" />
                            </x-slot>
                        </div>
                    @endif
                    <div class="flex flex-col gap-4">
                        <div class="flex gap-4 justify-between">
                            <div>
                                <label for="product_id_{{ $index }}"
                                    class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                    Código<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                                </label>
                                <x-filament::input.wrapper>
                                    <x-filament::input type="text"
                                        wire:model="productos.{{ $index }}.product_id"
                                        name="productos[{{ $index }}][product_id]"
                                        id="product_id_{{ $index }}" data-index="{{ $index }}"
                                        class="product_index-input" />
                                </x-filament::input.wrapper>
                            </div>
                            <div>
                                <label for="pieces_{{ $index }}"
                                    class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                    Piezas<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                                </label>
                                <x-filament::input.wrapper>
                                    <x-filament::input type="number"
                                        wire:model="productos.{{ $index }}.pieces"
                                        name="productos[{{ $index }}][pieces]"
                                        id="pieces_{{ $index }}" data-index="{{ $index }}"
                                        class="pieces_index-input" />
                                </x-filament::input.wrapper>
                            </div>
                            <div>
                                <label for="product_description_{{ $index }}"
                                    class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                    Descripción
                                </label>
                                <x-filament::input.wrapper disabled>
                                    <x-filament::input type="text"
                                        wire:model="productos.{{ $index }}.product_description"
                                        name="productos[{{ $index }}][product_description]"
                                        id="product_description_{{ $index }}" disabled readonly />
                                </x-filament::input.wrapper>
                            </div>
                            <div>
                                <label for="unit_price_{{ $index }}"
                                    class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                    Precio Unitario<span
                                        class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                                </label>
                                <x-filament::input.wrapper>
                                    <x-filament::input type="number"
                                        wire:model="productos.{{ $index }}.unit_price"
                                        name="productos[{{ $index }}][unit_price]"
                                        id="unit_price_{{ $index }}" data-index="{{ $index }}"
                                        class="unit_price_index-input" step="5" min="0" />
                                </x-filament::input.wrapper>
                            </div>
                            <div>
                                <label for="subtotal_{{ $index }}"
                                    class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                    Subtotal
                                </label>
                                <x-filament::input.wrapper disabled>
                                    <x-filament::input type="number"
                                        wire:model="productos.{{ $index }}.subtotal"
                                        name="productos[{{ $index }}][subtotal]"
                                        id="subtotal_{{ $index }}" class="subtotal_index-input" disabled
                                        readonly />
                                </x-filament::input.wrapper>
                            </div>
                        </div>
                    </div>
                </x-filament::section>
            @endforeach
            <!-- Contenedor del botón centrado -->
            <div class="flex justify-center mt-4">
                <x-filament::button type="button" class="addProduct" wire:click="addProduct" color="gray">
                    Agregar Producto
                </x-filament::button>
            </div>
        </x-filament::section>
        <br>
        <x-filament::section collapsible collapsed>
            <x-slot name="heading">
                Totales
            </x-slot>
            <div class="flex gap-4 justify-between">
                <div>
                    <label for="sender_total_update"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Total Remitente<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="number" wire:model="sender_total_update" name="sender_total_update"
                            id="sender_total_update" value="0.00" />
                    </x-filament::input.wrapper>
                </div>
                <div>
                    <label for="receiver_total_update"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Total Destinatario<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="number" wire:model="receiver_total_update"
                            name="receiver_total_update" id="receiver_total_update" value="0.00" />
                    </x-filament::input.wrapper>
                </div>
                <div>
                    <label for="total_update"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Monto Total<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="number" wire:model="total_update" name="total_update"
                            id="total_update" />
                    </x-filament::input.wrapper>
                </div>
            </div>
        </x-filament::section>
        <br>
    </x-filament::grid.column>

    {{-- FILA INFERIOR BOTONES --}}
    <x-filament::grid.column style="--col-span-default: span 5 / span 5;">
        <div class="flex justify-between items-center mb-4">
            {{-- Botón izquierdo --}}
            <div>
                <x-filament::button type="button" class="saveShipment" wire:click="confirmSaveUpdateGuide"
                    color="primary">
                    Actualizar Envío
                </x-filament::button>
            </div>
        </div>
    </x-filament::grid.column>
</x-filament::modal>
