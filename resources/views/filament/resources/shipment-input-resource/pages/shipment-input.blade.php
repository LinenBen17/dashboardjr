<x-filament-panels::page>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
    <!-- DataTables KeyTable -->
    <script src="https://cdn.datatables.net/keytable/2.11.0/js/dataTables.keyTable.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/keytable/2.11.0/css/keyTable.dataTables.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">

    <x-filament::grid class="gap-4" style="--cols-default: repeat(1, minmax(0, 1fr));">
        {{-- Información por defecto --}}
        <x-filament::section>
            <x-filament::grid class="gap-4" style="--cols-default: repeat(4, minmax(0, 1fr));">
                <div>
                    <label for="guia_madre"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        No. Guía<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="number" name="mother" id="guia_madre" value="{{ $no_guide_user }}" />
                    </x-filament::input.wrapper>
                </div>
                <div>
                    <label for="date_guide"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Fecha<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="date" wire:model="date_guide" name="date_guide" id="date_guide" />
                    </x-filament::input.wrapper>
                </div>
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
                <div>
                    <label for="manifest_code"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Código de Manifiesto<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" name="manifest_code" id="manifest_code" value="" />
                    </x-filament::input.wrapper>
                </div>
            </x-filament::grid>
        </x-filament::section>

        {{-- INFORMACIÓN REMITENTE Y DESTINATARIO --}}
        <x-filament::section>
            {{-- 4 columnas remitente --}}
            <div class="flex gap-4 justify-between">
                <div>
                    <label for="codigo_remitente"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Codigo Remitente<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="number" wire:model="sender_code" name="sender_code"
                            id="codigo_remitente" />
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
                    <x-filament::input.wrapper>
                        <x-filament::input type="number" wire:model="receiver_code" name="receiver_code"
                            id="codigo_destinatario" />
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
                        Dirección Destinatario<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" wire:model="receiver_address" name="receiver_address"
                            id="receiver_address" />
                    </x-filament::input.wrapper>
                </div>
                <div>
                    <label for="receiver_phone"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Teléfono Destinatario<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" wire:model="receiver_phone" name="receiver_phone"
                            id="receiver_phone" />
                    </x-filament::input.wrapper>
                </div>
            </div>
        </x-filament::section>

        {{-- INFORMACIÓN DESTINO --}}
        <x-filament::section>
            <div class="flex gap-4 justify-between">
                <div>
                    <label for="prefix_origen"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Origen<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" wire:model="prefix_origin" name="prefix_origin"
                            id="prefix_origen" value="{{ $prefix_origin }}" />
                    </x-filament::input.wrapper>
                </div>
                <br>
                <div>
                    <label for="prefix_destino"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Destino<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" wire:model="prefix_destination" name="prefix_destination"
                            id="prefix_destino" />
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

        {{-- INPUTS DINAMICOS --}}
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
                        <th class="border-b border-gray-300 dark:border-gray-600 px-4 py-2 text-center">Precio Unitario
                        </th>
                        <th class="border-b border-gray-300 dark:border-gray-600 px-4 py-2 text-center">Subtotal</th>
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

        {{-- Calculo de subtotales y total --}}
        <x-filament::section>
            <x-filament::grid class="gap-4" style="--cols-default: repeat(3, minmax(0, 1fr));">
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
                <div>
                    <label for="total"
                        class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        Monto Total<span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="number" wire:model="total" name="total" id="total" />
                    </x-filament::input.wrapper>
                </div>
            </x-filament::grid>
        </x-filament::section>


        {{-- FILA INFERIOR BOTONES --}}
        <x-filament::grid.column style="--col-span-default: span 1 / span 1;">
            <div class="flex justify-between items-center mb-4">
                {{-- Botón izquierdo --}}
                <div>
                    <x-filament::button type="button" class="saveShipment" wire:click="confirmSave"
                        color="primary">
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
            </x-filament::grxid.column>
    </x-filament::grid>

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


    {{-- MODAL CLIENTES TABLE --}}
    <x-filament::modal id="customersModal" width="4xl" :close-button="true" :close-by-escaping="false" :close-by-clicking-away="false">
        <table id="tablaClientes">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Phone</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </x-filament::modal>
    <script src="{{ asset('js/shipment_input.js') }}"></script>

</x-filament-panels::page>
