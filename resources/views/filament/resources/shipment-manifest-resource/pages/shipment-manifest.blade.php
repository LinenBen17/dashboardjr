<x-filament-panels::page>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
    <!-- DataTables KeyTable -->
    <script src="https://cdn.datatables.net/keytable/2.11.0/js/dataTables.keyTable.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/keytable/2.11.0/css/keyTable.dataTables.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">
    {{-- Botones derechos --}}
    <div class="flex space-x-2 justify-end gap-2">
        <div>
            <x-filament::button type="button" class="searchManifestButton" wire:click="openSearchManifest" color="info">
                Buscar Manifiesto
            </x-filament::button>
        </div>
        <div>
            <x-filament::button type="button" class="updateGuideButton" wire:click="openUpdateGuides" color="danger">
                Modificar Guía
            </x-filament::button>
        </div>
    </div>
    {{-- FORMULARIO DEL MANIFIESTO --}}
    <x-filament::section>
        <div class="flex gap-4 justify-between">
            <div>
                <label for="manifest_code"
                    class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                    Código de Manifiesto<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                </label>
                <x-filament::input.wrapper>
                    <x-filament::input type="text" wire:model="manifest_code" name="manifest_code" id="manifest_code"
                        value="" />
                </x-filament::input.wrapper>
            </div>
            <br>
            <div>
                <label for="date"
                    class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                    Fecha Manifiesto<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                </label>
                <x-filament::input.wrapper>
                    <x-filament::input type="date" wire:model="date" name="date" id="date" />
                </x-filament::input.wrapper>
            </div>
            <br>
            <div>
                <label for="route_id"
                    class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                    Ruta de Destino<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                </label>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model="route_id" name="route_id" id="route_id"
                        class="fi-fo-field-input block w-full rounded-lg border-gray-300">
                        <option value="">Seleccione una opción</option>
                        @foreach ($routes as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
            <div>
                <label for="agency_origin_id"
                    class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                    Agencia de Origen<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                </label>
                <x-filament::input.wrapper disabled>
                    <x-filament::input.select wire:model="agency_origin_id" name="agency_origin_id"
                        id="agency_origin_id" class="fi-fo-field-input block w-full rounded-lg border-gray-300" disabled
                        readonly>
                        @foreach ($agencies_origin as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
            <div>
                <label for="agency_destination_id"
                    class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                    Agencia de Destino<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                </label>
                <x-filament::input.wrapper disabled>
                    <x-filament::input.select wire:model="agency_destination_id" name="agency_destination_id"
                        id="agency_destination_id" class="fi-fo-field-input block w-full rounded-lg border-gray-300"
                        disabled readonly>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
        </div>
    </x-filament::section>
    {{-- TABLA DE GUÍAS ASIGNADAS AL MANIFIESTO --}}
    <x-filament::section>
        <div class="flex justify-between items-center mb-4">
            <div class="">
                <h2 class="text-lg font-bold mb-4">
                    Guías del Manifiesto
                    <span id="manifest_repeat_message" class="text-orange-600 dark:text-yellow-400"></span>
                </h2>
            </div>
            <div>
                <x-filament::button type="button" class="saveManifest" wire:click="confirmSave" color="primary">
                    <div class="flex items-center gap-2">
                        Imprimir
                        <x-heroicon-o-printer class="w-5 h-5" />
                    </div>
                </x-filament::button>
                <x-filament::button type="button" class="unlinkGuides" wire:click="unlinkGuides" color="warning">
                    <div class="flex items-center gap-2">
                        Desenlazar
                        <x-heroicon-o-link-slash class="w-5 h-5" />
                    </div>
                </x-filament::button>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm table-fixed">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-2 text-center" style="width: 10ch;">No. Guía</th>
                        <th class="px-4 py-2 text-left" style="width: 45ch;">Destinatario</th>
                        <th class="px-4 py-2 text-center" style="width: 7ch;">Destino</th>
                        <th class="px-4 py-2 text-center" style="width: 15ch;">Forma de Pago</th>
                        <th class="px-4 py-2 text-center" style="width: 3ch;">Piezas</th>
                        <th class="px-4 py-2 text-center" style="width: 10ch;">Monto</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700" id="tbodyGuidesManifests">
                    {{-- Aquí se llenarán las filas dinámicamente con JavaScript --}}
                </tbody>
                <tfoot id="tfootGuidesManifests">
                    {{-- Aquí se llennarán las filas dinámicamente con JavaScript --}}
                </tfoot>
            </table>
        </div>

    </x-filament::section>

    {{-- MODAL MODIFICACION DE GUÍAS --}}
    <x-filament::modal id="updateGuides" width="4xl">
        <x-slot name="heading">
            Modificación de Guías
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
                            <x-filament::input.select wire:model="payment_method_id_update"
                                name="payment_method_id_update" id="payment_method_id_update"
                                class="fi-fo-field-input block w-full rounded-lg border-gray-300">
                                <option value="">Seleccione una opción</option>
                                @foreach ($payment_methods as $id => $name)
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
                            <x-filament::input type="text" wire:model="manifest_code_update"
                                name="manifest_code_update" id="manifest_code_update" />
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
                            <x-filament::input type="number" wire:model="sender_code_update"
                                name="sender_code_update" id="codigo_remitente_update" />
                        </x-filament::input.wrapper>
                    </div>
                    <div>
                        <label for="sender_name_update"
                            class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            Nombre Remitente<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                        </label>
                        <x-filament::input.wrapper>
                            <x-filament::input type="text" wire:model="sender_name_update"
                                name="sender_name_update" id="sender_name_update" />
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
                            <x-filament::input type="text" wire:model="sender_phone_update"
                                name="sender_phone_update" id="sender_phone_update" />
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
                            <x-filament::input type="number" wire:model="receiver_code_update"
                                name="receiver_code_update" id="codigo_destinatario_update" />
                        </x-filament::input.wrapper>
                    </div>
                    <div>
                        <label for="receiver_name_update"
                            class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            Nombre Destinatario<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                        </label>
                        <x-filament::input.wrapper>
                            <x-filament::input type="text" wire:model="receiver_name_update"
                                name="receiver_name_update" id="receiver_name_update" />
                        </x-filament::input.wrapper>
                    </div>
                    <div>
                        <label for="receiver_address_update"
                            class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            Dirección Destinatario<span
                                class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                        </label>
                        <x-filament::input.wrapper>
                            <x-filament::input type="text" wire:model="receiver_address_update"
                                name="receiver_address_update" id="receiver_address_update" />
                        </x-filament::input.wrapper>
                    </div>
                    <div>
                        <label for="receiver_phone_update"
                            class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            Teléfono Destinatario<span
                                class="text-danger-600 dark:text-danger-400 font-medium">*</span>
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
            <x-filament::section>
                <label for=""
                    class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                    Productos
                </label>
                <div class="flex gap-4" style="justify-content: space-evenly;">
                    <div class="flex gap-4 justify-between">
                        <div>
                            <label for="product_id_input"
                                class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                Código<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                            </label>
                            <x-filament::input.wrapper>
                                <x-filament::input type="text" wire:model.defer="newProduct.product_id"
                                    id="product_id_input" />
                            </x-filament::input.wrapper>
                        </div>
                        <div>
                            <label for="pieces_input"
                                class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                Piezas<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                            </label>
                            <x-filament::input.wrapper>
                                <x-filament::input type="number" wire:model.defer="newProduct.pieces"
                                    id="pieces_input" />
                            </x-filament::input.wrapper>
                        </div>
                        <div>
                            <label for="product_description_input"
                                class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                Descripción
                            </label>
                            <x-filament::input.wrapper disabled>
                                <x-filament::input type="text" wire:model.defer="newProduct.product_description"
                                    id="product_description_input" disabled readonly />
                            </x-filament::input.wrapper>
                        </div>
                        <div>
                            <label for="unit_price_input"
                                class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                Precio Unitario<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                            </label>
                            <x-filament::input.wrapper>
                                <x-filament::input type="number" wire:model.defer="newProduct.unit_price"
                                    id="unit_price" />
                            </x-filament::input.wrapper>
                        </div>


                    </div>
                    <div class="flex items-end">
                        <x-filament::button type="button" class="addProduct" wire:click="addProduct">
                            Agregar Producto
                        </x-filament::button>
                    </div>
                </div>
                <table class="w-full mt-4 text-sm border-collapse">
                    <thead>
                        <tr>
                            <th class="border-b border-gray-300 dark:border-gray-600 px-4 py-2 text-center">Código</th>
                            <th class="border-b border-gray-300 dark:border-gray-600 px-4 py-2 text-center">Piezas</th>
                            <th class="border-b border-gray-300 dark:border-gray-600 px-4 py-2 text-center">Descripción
                            </th>
                            <th class="border-b border-gray-300 dark:border-gray-600 px-4 py-2 text-center">Precio
                                Unitario
                            </th>
                            <th class="border-b border-gray-300 dark:border-gray-600 px-4 py-2 text-center">Subtotal
                            </th>
                            <th class="border-b border-gray-300 dark:border-gray-600 px-4 py-2 text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productos as $index => $producto)
                            <tr>
                                <td class="border-b border-gray-300 dark:border-gray-600 px-4 py-2 text-center">
                                    {{ $producto['product_id'] }}</td>
                                <td class="border-b border-gray-300 dark:border-gray-600 px-4 py-2 text-center">
                                    {{ $producto['pieces'] }}
                                </td>
                                <td class="border-b border-gray-300 dark:border-gray-600 px-4 py-2 text-center">
                                    {{ $producto['product_description'] }}</td>
                                <td class="border-b border-gray-300 dark:border-gray-600 px-4 py-2 text-center">
                                    {{ $producto['unit_price'] }}</td>
                                <td class="border-b border-gray-300 dark:border-gray-600 px-4 py-2 text-center">
                                    {{ $producto['subtotal'] }}</td>
                                <td class="border-b border-gray-300 dark:border-gray-600 px-4 py-2 text-center">
                                    <button wire:click="removeProduct({{ $index }})">
                                        ❌
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
                            <x-filament::input type="number" wire:model="sender_total_update"
                                name="sender_total_update" id="sender_total_update" value="0.00" />
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
                <div>
                    <x-filament::button type="button" class="deleteShipment" color="danger"
                        wire:click="confirmDeleteShipment">
                        Eliminar Envío
                    </x-filament::button>
                </div>
            </div>
        </x-filament::grid.column>
    </x-filament::modal>

    {{-- MODAL GUIAS HIJAS --}}
    <x-filament::modal id="childGuides" width="4xl">
        <x-slot name="heading">
            Enlace de Guías Hijas
        </x-slot>
        {{-- INPUT Y CONTADOR --}}
        <x-filament::grid class="gap-4" style="--cols-default: repeat(2, minmax(0, 1fr));">
            <x-filament::grid.column style="--col-span-default: span 1 / span 1;">
                <div>
                    <label for="child"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Guía Hija
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" id="child" name="child"
                            placeholder="Escanee las guías hijas" />
                    </x-filament::input.wrapper>
                    <p class="fi-fo-field-wrp-helper-text mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Estas guías hijas estarán enlazadas al envío.
                    </p>
                </div>
            </x-filament::grid.column>
            <x-filament::grid.column style="--col-span-default: span 1 / span 1;">
                <div class="flex flex-col">
                    <span class="text-white text-sm font-medium">Guías Hijas Enlazadas</span>
                    <span id="guia-hija-count" class="text-white"
                        style="font-size: 24pt">{{ count($childGuides) }}</span>
                </div>
            </x-filament::grid.column>
            <div class="overflow-y-auto" style="max-height: 300px">
                @if (count($childGuides) > 0)
                    <ul class="space-y-2">
                        @foreach ($childGuides as $child)
                            <li class="flex justify-between items-center p-2 bg-gray-100 dark:bg-gray-700 rounded-md">
                                <span class="text-gray-900 dark:text-white">{{ $child }}</span>
                                <button type="button" class="text-red-600 hover:text-red-800"
                                    wire:click="removeChildGuide('{{ $child }}')">
                                    Eliminar
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 dark:text-gray-400">
                        No hay guías hijas enlazadas.
                    </p>
                @endif
            </div>

        </x-filament::grid>
        <div>
            <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                <button x-data="{
                    state: false,
                    toggle() {
                        this.state = !this.state;
                        window.dispatchEvent(new CustomEvent('toggle-changed', { detail: this.state }));
                    }
                }" x-bind:aria-checked="state.toString()" x-on:click="toggle()"
                    x-bind:class="state
                        ?
                        'bg-custom-600 fi-color-success' :
                        'bg-gray-200 dark:bg-gray-700 fi-color-gray'"
                    x-bind:style="state
                        ?
                        '--c-600:var(--success-600)' :
                        '--c-600:var(--gray-600)'"
                    class="fi-fo-toggle relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent outline-none transition-colors duration-200 ease-in-out focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-1 disabled:pointer-events-none disabled:opacity-70 dark:focus-visible:ring-primary-500 dark:focus-visible:ring-offset-gray-900"
                    role="switch" type="button">
                    <span
                        class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                        x-bind:class="state ? 'translate-x-5 rtl:-translate-x-5' : 'translate-x-0'"></span>

                    <!-- hidden input to send value -->
                    <input type="hidden" name="linkLater" x-bind:value="state">
                </button>

                <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                    Enlazar Después
                </span>
            </label>
        </div>
        <div class="">
            <x-filament::button type="button" class="saveChilds" wire:click="confirmChilds" color="primary">
                Enlazar
            </x-filament::button>
        </div>
    </x-filament::modal>

    {{-- MODAL PARA BUSCAR MANIFIESTOS --}}
    <x-filament::modal id="searchManifest" width="4xl">
        <x-slot name="heading">
            Búsqueda de Manifiestos
        </x-slot>
        <x-filament::grid class="gap-4" style="--cols-default: repeat(5, minmax(0, 1fr));">
            <x-filament::grid.column style="--col-span-default: span 1 / span 1;">
                <div>
                    <label for="agency_origin_manifest_search"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Agencia de Origen<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model="agency_origin_manifest_search"
                            name="agency_origin_manifest_search" id="agency_origin_manifest_search"
                            class="fi-fo-field-input block w-full rounded-lg border-gray-300">
                            <option value="">Seleccione una opción</option>
                            @foreach ($agencies as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            </x-filament::grid.column>
            <x-filament::grid.column style="--col-span-default: span 1 / span 1;">
                <div>
                    <label for="agency_destination_manifest_search"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Agencia de Destino<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model="agency_destination_manifest_search"
                            name="agency_destination_manifest_search" id="agency_destination_manifest_search"
                            class="fi-fo-field-input block w-full rounded-lg border-gray-300">
                            <option value="">Seleccione una opción</option>
                            @foreach ($agencies as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            </x-filament::grid.column>
            <x-filament::grid.column style="--col-span-default: span 1 / span 1;">

                <div>
                    <label for="route_destination_manifest_search"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Ruta de Destino<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select type="text" wire:model="route_destination_manifest_search"
                            name="route_destination_manifest_search" id="route_destination_manifest_search"
                            class="fi-fo-field-input block w-full rounded-lg border-gray-300" />
                    </x-filament::input.wrapper>
                </div>
            </x-filament::grid.column>
            <x-filament::grid.column style="--col-span-default: span 1 / span 1;">
                <div>
                    <label for="manifest_date_manifest_search"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Fecha de Manifiesto<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" name="manifest_date_manifest_search"
                            id="manifest_date_manifest_search" placeholder="dd/mm/aaaa" />
                    </x-filament::input.wrapper>
                </div>
            </x-filament::grid.column>
            <x-filament::grid.column style="--col-span-default: span 1 / span 1;">
                <div class="flex items-end">
                    <x-filament::button type="button" class="rePrintManifest" color="primary">
                        <div class="flex items-center gap-2">
                            Re-Imprimir
                            <x-heroicon-o-printer class="w-5 h-5" />
                        </div>
                    </x-filament::button>
                </div>
            </x-filament::grid.column>
        </x-filament::grid>
    </x-filament::modal>
    <script>
        document.addEventListener('input', function(e) {
            if (e.target.id === 'manifest_date_manifest_search') {
                let value = e.target.value.replace(/\D/g, ''); // solo números

                if (value.length > 8) value = value.slice(0, 8);

                let formatted = value;

                if (value.length > 4) {
                    formatted = value.slice(0, 2) + '/' + value.slice(2, 4) + '/' + value.slice(4);
                } else if (value.length > 2) {
                    formatted = value.slice(0, 2) + '/' + value.slice(2);
                }

                e.target.value = formatted;
            }
        });
    </script>

    <script src="{{ asset('js/shipment_manifest.js') }}"></script>
</x-filament-panels::page>
