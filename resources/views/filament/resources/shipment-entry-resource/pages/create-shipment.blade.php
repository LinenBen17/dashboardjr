<x-filament-panels::page>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
    <!-- DataTables KeyTable -->
    <script src="https://cdn.datatables.net/keytable/2.11.0/js/dataTables.keyTable.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/keytable/2.11.0/css/keyTable.dataTables.min.css">

    {{-- Tom Select --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">

    <style>
        /* Contenedor principal */
        .ts-control {
            border: none !important;
            box-shadow: none !important;
            /* padding: 0 !important; */
            background: transparent !important;
        }


        .ts-control {
            min-height: 2.3rem;
            /* igual a Filament */
        }

        /* Input interno */
        .ts-control input {
            font-size: 0.875rem;
            line-height: 1.5rem;
        }

        .ts-wrapper {
            width: 100% !important;
        }

        .ts-control {
            width: 100% !important;
        }

        /* Texto seleccionado (light mode) */
        .ts-control .item {
            color: rgb(17 24 39);
            /* mismo que Filament */
        }

        /* Texto seleccionado (dark mode) */
        .dark .ts-control .item {
            color: rgb(255 255 255);
        }

        .fi-input-wrp-input .ts-wrapper {
            flex: 1;
        }

        .dark .ts-control input {
            color: rgb(255 255 255);
        }

        .dark .ts-control input::placeholder {
            color: rgb(255 255 255) !important;
        }

        /* Dropdown */
        .ts-dropdown {
            border-radius: 0.75rem;
            /* rounded-xl */
            border: 1px solid rgb(229 231 235);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        /* Opciones */
        .ts-dropdown .option {
            padding: 8px 12px;
        }

        .dark .ts-dropdown {
            background-color: rgb(31 41 55);
            border-color: rgb(75 85 99);
        }

        .dark .ts-dropdown .option {
            color: white;
        }

        .dark .ts-dropdown .option:hover {
            background-color: rgb(75 85 99);
        }

        .dark .ts-dropdown .option.active {
            background-color: rgb(75 85 99);
        }
    </style>

    <x-filament::grid class="gap-4" style="--cols-default: repeat(5, minmax(0, 1fr));">
        {{-- COLUMNA IZQUIERDA --}}
        <x-filament::grid.column style="--col-span-default: span 1 / span 1;">
            {{-- Primer Contenedor / Información por defecto --}}
            <x-filament::section>
                <div>
                    <label for="guia_madre"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        No. Guía<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper disabled>
                        <x-filament::input type="number" name="mother" id="guia_madre" value="{{ $no_guide_user }}"
                            disabled readonly />
                    </x-filament::input.wrapper>
                </div>
                <br>
                <div>
                    <label for="date_guide"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Fecha<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper disabled>
                        <x-filament::input type="date" wire:model="date_guide" name="date_guide" id="date_guide"
                            disabled readonly />
                    </x-filament::input.wrapper>
                </div>
                <br>
                <div>
                    <label for="forma_pago"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Forma de Pago<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model="payment_method_id" name="payment_method_id"
                            id="forma_pago" class="fi-fo-field-input block w-full rounded-lg border-gray-300">
                            <option value="">Seleccione una opción</option>
                            @foreach ($payment_methods as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            </x-filament::section>
            <br>

            {{-- Segundo contenedor / Calculo de subtotales y total --}}
            <x-filament::section>
                <div>
                    <label for="sender_total"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Total Remitente<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="number" wire:model="sender_total" name="sender_total"
                            id="sender_total" value="0.00" />
                    </x-filament::input.wrapper>
                </div>
                <br>
                <div>
                    <label for="receiver_total"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Total Destinatario<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="number" wire:model="receiver_total" name="receiver_total"
                            id="receiver_total" value="0.00" />
                    </x-filament::input.wrapper>
                </div>
                <br>
                <div>
                    <label for="total"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Monto Total<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="number" wire:model="total" name="total" id="total" />
                    </x-filament::input.wrapper>
                </div>
            </x-filament::section>
            <br>
        </x-filament::grid.column>

        {{-- COLUMNA DERECHA --}}
        <x-filament::grid.column style="--col-span-default: span 4 / span 4;">

            {{-- TERCER CONTENEDOR / INFORMACIÓN REMITENTE Y DESTINATARIO --}}
            <x-filament::section>
                {{-- 4 columnas remitente --}}
                <div class="flex gap-4 justify-between">
                    <div>
                        <label for="codigo_remitente"
                            class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            Codigo Remitente<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                        </label>
                        <x-filament::input.wrapper wire:ignore>
                            <input type="text" id="codigo_remitente" name="sender_code" wire:model="sender_code" />
                        </x-filament::input.wrapper>
                    </div>
                    <div>
                        <label for="sender_name"
                            class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            Nombre Remitente<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                        </label>
                        <x-filament::input.wrapper>
                            <x-filament::input type="text" wire:model="sender_name" name="sender_name"
                                id="sender_name" />
                        </x-filament::input.wrapper>
                    </div>
                    <div>
                        <label for="sender_address"
                            class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            Dirección Remitente<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                        </label>
                        <x-filament::input.wrapper>
                            <x-filament::input type="text" wire:model="sender_address" name="sender_address"
                                id="sender_address" />
                        </x-filament::input.wrapper>
                    </div>
                    <div>
                        <label for="sender_phone"
                            class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            Teléfono Remitente<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                        </label>
                        <x-filament::input.wrapper>
                            <x-filament::input type="text" wire:model="sender_phone" name="sender_phone"
                                id="sender_phone" />
                        </x-filament::input.wrapper>
                    </div>
                </div>
                <br>
                {{-- 4 columnas destinatario --}}
                <div class="flex gap-4 justify-between">
                    <div>
                        <label for="codigo_destinatario"
                            class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            Codigo Destinatario<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                        </label>
                        <x-filament::input.wrapper wire:ignore>
                            <input type="text" id="codigo_destinatario" name="receiver_code"
                                wire:model="receiver_code" />
                        </x-filament::input.wrapper>
                    </div>
                    <div>
                        <label for="receiver_name"
                            class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            Nombre Destinatario<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                        </label>
                        <x-filament::input.wrapper>
                            <x-filament::input type="text" wire:model="receiver_name" name="receiver_name"
                                id="receiver_name" />
                        </x-filament::input.wrapper>
                    </div>
                    <div>
                        <label for="receiver_address"
                            class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            Dirección Destinatario<span
                                class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                        </label>
                        <x-filament::input.wrapper>
                            <x-filament::input type="text" wire:model="receiver_address" name="receiver_address"
                                id="receiver_address" />
                        </x-filament::input.wrapper>
                    </div>
                    <div>
                        <label for="receiver_phone"
                            class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            Teléfono Destinatario<span
                                class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                        </label>
                        <x-filament::input.wrapper>
                            <x-filament::input type="text" wire:model="receiver_phone" name="receiver_phone"
                                id="receiver_phone" />
                        </x-filament::input.wrapper>
                    </div>
                </div>
            </x-filament::section>

            <br>

            {{-- CUARTO CONTENEDOR / INFORMACIÓN DESTINO --}}
            <x-filament::section>
                <div class="flex gap-4 justify-between">
                    <div>
                        <label for="prefix_origen"
                            class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            Origen<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                        </label>
                        <x-filament::input.wrapper disabled>
                            <x-filament::input type="text" wire:model="prefix_origin" name="prefix_origin"
                                id="prefix_origen" value="{{ $prefix_origin }}" disabled readonly />
                        </x-filament::input.wrapper>
                    </div>
                    <br>
                    <div>
                        <label for="prefix_destino"
                            class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            Destino<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                        </label>
                        <x-filament::input.wrapper>
                            <x-filament::input type="text" wire:model="prefix_destination"
                                name="prefix_destination" id="prefix_destino" />
                        </x-filament::input.wrapper>
                    </div>
                    <br>
                    <div>
                        <label for="town_id"
                            class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            Municipio<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                        </label>
                        <x-filament::input.wrapper>
                            <x-filament::input.select wire:ignore name="town_id" id="town_id">
                                <option value="">Seleccione una opción</option>
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>
                    <div>
                        <label for="route_destino"
                            class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            Ruta<span class="text-danger-600 dark:text-danger-400 font-medium"></span>
                        </label>
                        <x-filament::input.wrapper disabled>
                            <x-filament::input type="text" wire:model="route_destination" name="route_destination"
                                id="route_destino" disabled readonly />
                        </x-filament::input.wrapper>
                    </div>
                </div>
            </x-filament::section>
            <br>
            {{-- QUINTO CONTENEDOR / INPUTS DINAMICOS --}}
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
                            Agregar
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
        </x-filament::grid.column>
        {{-- FILA INFERIOR BOTONES --}}
        <x-filament::grid.column style="--col-span-default: span 5 / span 5;">
            <div class="flex justify-between items-center mb-4">
                {{-- Botón izquierdo --}}
                <div>
                    <x-filament::button type="button" class="saveShipment" color="primary">
                        Guardar Envío
                    </x-filament::button>
                </div>

                {{-- Botones derechos --}}
                <div class="flex space-x-2">
                    <x-filament::button type="button" class="consultGuideButton" wire:click="openConsultGuides"
                        color="info">
                        Consultar Guía
                    </x-filament::button>
                </div>
            </div>
        </x-filament::grid.column>
    </x-filament::grid>

    {{-- MODAL GUIAS HIJAS --}}
    <x-child-guides-modal :count="count($childGuides)" :childGuides="$childGuides" action="confirmChilds" />

    {{-- MODAL CONSULTA DE GUÍAS --}}
    <x-filament::modal id="consultGuides" width="4xl">
        <x-slot name="heading">
            Consulta de Guías
        </x-slot>

        {{-- Input de búsqueda --}}
        <div class="mb-4">
            <label for="search_guide"
                class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                No. Guía
                <span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
            </label>

            <div class="flex gap-2 items-center">
                <x-filament::input.wrapper class="flex-1">
                    <x-filament::input type="text" placeholder="Guía Madre: 1234567, Guía Hija: H0001234"
                        name="guide" id="search_guide" />
                </x-filament::input.wrapper>

                <x-filament::button id="searchGuideBtn" color="primary">
                    Buscar
                </x-filament::button>
            </div>
        </div>

        {{-- Información principal --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            {{-- Remitente --}}
            <div class="p-4 border rounded-lg bg-gray-50 dark:bg-gray-800">
                <h3 class="text-lg font-semibold mb-2">Remitente</h3>
                <div class="grid grid-cols-2 gap-2">
                    <p><span class="font-medium">Nombre:</span> <span id="sender_name_consult">-</span></p>
                    <p><span class="font-medium">Dirección:</span> <span id="sender_address_consult">-</span></p>
                    <p><span class="font-medium">Teléfono:</span> <span id="sender_phone_consult">-</span></p>
                </div>
            </div>

            {{-- Destinatario --}}
            <div class="p-4 border rounded-lg bg-gray-50 dark:bg-gray-800">
                <h3 class="text-lg font-semibold mb-2">Destinatario</h3>
                <div class="grid grid-cols-2 gap-2">
                    <p><span class="font-medium">Nombre:</span> <span id="receiver_name_consult">-</span></p>
                    <p><span class="font-medium">Dirección:</span> <span id="receiver_address_consult">-</span></p>
                    <p><span class="font-medium">Teléfono:</span> <span id="receiver_phone_consult">-</span></p>
                </div>
            </div>

            {{-- Detalles del envío --}}
            <div class="p-4 border rounded-lg bg-gray-50 dark:bg-gray-800">
                <h3 class="text-lg font-semibold mb-2">Detalles del Envío</h3>
                <div class="grid grid-cols-2 gap-2">
                    <p><span class="font-medium">Producto:</span> <span id="product_consult">-</span></p>
                    <p><span class="font-medium">Piezas:</span> <span id="pieces_consult">-</span></p>
                    <p><span class="font-medium">Precio Unitario:</span> Q.<span id="unit_price_consult">-</span></p>
                    <p><span class="font-medium">Total:</span> Q.<span id="total_consult">-</span></p>
                    <p><span class="font-medium">Fecha de Guía:</span> <span id="date_guide_consult">-</span></p>
                    <p><span class="font-medium">Forma de Pago:</span> <span id="payment_method_consult">-</span></p>
                    <p><span class="font-medium">No. Manifiesto:</span> <span id="manifest_no_consult">-</span></p>
                </div>
            </div>
        </div>

        {{-- Tracking --}}
        <div class="p-4 border rounded-lg bg-gray-50 dark:bg-gray-800">
            <h3 class="text-lg font-semibold mb-2">Tracking</h3>

            <div class="overflow-y-auto max-h-64">
                <table
                    class="min-w-full table-auto border-collapse border border-gray-200 dark:border-gray-700 text-sm">
                    <thead class="bg-gray-200 dark:bg-gray-700">
                        <tr>
                            <th class="px-2 py-1 border">Fecha / Hora</th>
                            <th class="px-2 py-1 border">Ubicación</th>
                            <th class="px-2 py-1 border">Estado</th>
                            <th class="px-2 py-1 border">Usuario</th>
                            <th class="px-2 py-1 border">Comentario</th>
                        </tr>
                    </thead>
                    <tbody id="tracking_table_body">

                    </tbody>
                </table>
            </div>
        </div>
    </x-filament::modal>


    {{-- MODAL CLIENTES CON TARIFA ESPECIAL --}}
    <x-filament::modal id="specialRatesModal" width="4xl" :close-button="true" :close-by-clicking-away="false" slide-over="true">
        <div>
            <x-slot name="heading">
                Clientes con Tarifa Especial
            </x-slot>
            <x-slot name="description">
                Selecciona los productos a agregar
            </x-slot>

            @foreach ($customer_data_prices as $customer)
                <x-filament::section>

                    {{-- Datos del cliente --}}
                    <h3 class="font-bold text-lg">
                        {{ $customer['customer_data']['name'] }}
                    </h3>
                    <p class="text-sm text-gray-500">
                        {{ $customer['customer_data']['address'] }}
                    </p>

                    {{-- Condición --}}
                    @if (!empty($customer['special_rates']))
                        <div class="mt-3 space-y-2">
                            @foreach ($customer['special_rates'] as $rate)
                                @php $id = $rate['product_id']; @endphp
                                <div wire:key="special-rate-{{ $rate['product_id'] }}" class="flex gap-4"
                                    style="justify-content: space-evenly;">
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox"
                                            wire:model.defer="newSpecialProducts.{{ $id }}.selected"
                                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    </label>
                                    <div class="flex gap-4 justify-between">
                                        <div>
                                            <label for="product_code_input"
                                                class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                                Código<span
                                                    class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                                            </label>
                                            <x-filament::input.wrapper disabled>
                                                <x-filament::input type="text"
                                                    wire:model.defer="newSpecialProducts.{{ $id }}.product_code"
                                                    placeholder="{{ $rate['product_code'] }}" disabled readonly />
                                            </x-filament::input.wrapper>
                                        </div>
                                        <div>
                                            <label for="pieces_input"
                                                class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                                Piezas<span
                                                    class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                                            </label>
                                            <x-filament::input.wrapper>
                                                <x-filament::input type="number"
                                                    wire:model.defer="newSpecialProducts.{{ $id }}.pieces" />
                                            </x-filament::input.wrapper>
                                        </div>
                                        <div>
                                            <label for="product_description_input"
                                                class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                                Descripción
                                            </label>
                                            <x-filament::input.wrapper disabled>
                                                <x-filament::input type="text"
                                                    wire:model.defer="newSpecialProducts.product_description"
                                                    id="product_description_input"
                                                    placeholder="{{ $rate['product_name'] }}" disabled readonly />
                                            </x-filament::input.wrapper>
                                        </div>
                                        <div>
                                            <label for="unit_price_input"
                                                class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                                Precio Unitario<span
                                                    class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                                            </label>
                                            <x-filament::input.wrapper disabled>
                                                <x-filament::input type="number"
                                                    wire:model.defer="newSpecialProducts.{{ $id }}.unit_price"
                                                    placeholder="{{ $rate['special_price'] }}" disabled readonly />
                                            </x-filament::input.wrapper>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-3 text-sm text-gray-400">
                            Este cliente no cuenta con tarifas especiales asignadas
                        </p>
                    @endif

                </x-filament::section>
            @endforeach
        </div>
        <div class="flex items-end">
            <x-filament::button type="button" class="addProductSpecial" wire:click="addProductSpecial">
                Agregar Producto
            </x-filament::button>
        </div>
    </x-filament::modal>

    {{-- MODAL FORMULARIO PAGO CONTRA ENTREGA --}}
    <x-filament::modal id="pceModal" width="2xl" :close-by-clicking-away="false">
        <x-filament::section class="pceModal">
            <h2 class="text-lg font-bold mb-4">Pago Contra Entrega</h2>
            <x-filament::grid class="gap-4" style="--cols-default: repeat(3, minmax(0, 1fr));">
                <x-filament::grid.column style="--col-span-default: span 1 / span 1;">
                    <label for="no_pce"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        No. Contra Entrega<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="number" wire:model.defer="pce_data.no_pce" id="no_pce" />
                    </x-filament::input.wrapper>
                </x-filament::grid.column>
            </x-filament::grid>
            <br>

            <div class="flex gap-4 justify-between">
                {{-- Valor --}}
                <div>
                    <label for="pce_amount"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Valor total a cobrar (Q)<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="number" wire:model.defer="pce_data.pce_amount" id="pce_amount" />
                    </x-filament::input.wrapper>
                </div>

                {{-- Piezas --}}
                <div>
                    <label for="pce_pieces"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Cantidad de piezas
                        <span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="number" wire:model.defer="pce_data.pce_pieces" id="pce_pieces"
                            value="1" />
                    </x-filament::input.wrapper>
                </div>

                {{-- Envío --}}
                <div>
                    <label for="pce_shipment_price"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Costo de envío (Q)
                        <span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="number" wire:model.defer="pce_data.pce_shipment_price"
                            id="pce_shipment_price" />
                    </x-filament::input.wrapper>
                </div>
            </div>
            <br>

            {{-- Radios --}}
            <div class="flex flex-col justify-end">
                <label class="text-sm font-medium mb-1">¿Quién paga el envío?</label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="radio" name="pce_shipment_pay" value="receiver"
                        class="border-gray-300 text-primary-600 focus:ring-primary-500"
                        wire:model.defer="pce_data.shipment_paid_by" checked>
                    Destinatario
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="radio" name="pce_shipment_pay" value="sender"
                        class="border-gray-300 text-primary-600 focus:ring-primary-500"
                        wire:model.defer="pce_data.shipment_paid_by">
                    Remitente
                </label>
            </div>

            {{-- Comisión --}}
            <div class="mt-4">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" id="pce_customer_commission"
                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                        wire:model.defer="pce_data.commission_paid_by">
                    Incluir comisión al destinatario (5%)
                </label>
            </div>

            {{-- Resultado --}}
            <div id="ce_results"
                class="mt-6 p-4 rounded-xl bg-gray-50 dark:bg-gray-800 border dark:border-gray-600 text-sm leading-relaxed">
            </div>
            <br>

            <div class="flex items-end">
                <x-filament::button type="button" class="saveCOData" wire:click="addCODProduct">
                    Guardar Información
                </x-filament::button>
            </div>
        </x-filament::section>
    </x-filament::modal>


    <script src="{{ asset('js/shipment_entries.js') }}"></script>
</x-filament-panels::page>
