<x-filament-panels::page>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    {{-- Informacion General --}}
    <x-filament::section collapsible>
        <x-slot name="heading">
            Información General de Envío
        </x-slot>
        {{-- Input de búsqueda --}}
        <div class="mb-4">
            <label for="search_guide"
                class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                No. Guía
                <span class="text-danger-600 dark:text-danger-400 font-medium">*</span>
            </label>

            <div class="flex gap-2 items-center">
                <x-filament::input.wrapper class="w-64"> <!-- ancho fijo de 16rem -->
                    <x-filament::input type="text" placeholder="Guía Madre: 1234567" wire:model="shipment_entry_guide"
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
                    <p><span class="font-medium">Nombre:</span> <span id="sender_name_consult" wire:ignore>-</span></p>
                    <p><span class="font-medium">Dirección:</span> <span id="sender_address_consult"
                            wire:ignore>-</span></p>
                    <p><span class="font-medium">Teléfono:</span> <span id="sender_phone_consult" wire:ignore>-</span>
                    </p>
                </div>
            </div>

            {{-- Destinatario --}}
            <div class="p-4 border rounded-lg bg-gray-50 dark:bg-gray-800">
                <h3 class="text-lg font-semibold mb-2">Destinatario</h3>
                <div class="grid grid-cols-2 gap-2">
                    <p><span class="font-medium">Nombre:</span> <span id="receiver_name_consult" wire:ignore>-</span>
                    </p>
                    <p><span class="font-medium">Dirección:</span> <span id="receiver_address_consult"
                            wire:ignore>-</span></p>
                    <p><span class="font-medium">Teléfono:</span> <span id="receiver_phone_consult" wire:ignore>-</span>
                    </p>
                </div>
            </div>

            {{-- Detalles del envío --}}
            <div class="p-4 border rounded-lg bg-gray-50 dark:bg-gray-800">
                <h3 class="text-lg font-semibold mb-2">Detalles del Envío</h3>
                <div class="grid grid-cols-2 gap-2">
                    <p><span class="font-medium">Producto:</span> <span id="product_consult" wire:ignore>-</span></p>
                    <p><span class="font-medium">Piezas:</span> <span id="pieces_consult" wire:ignore>-</span></p>
                    <p><span class="font-medium">Precio Unitario:</span> Q.<span id="unit_price_consult"
                            wire:ignore>-</span>
                    </p>
                    <p><span class="font-medium">Total:</span> Q.<span id="total_consult" wire:ignore>-</span></p>
                    <p><span class="font-medium">Fecha de Guía:</span> <span id="date_guide_consult"
                            wire:ignore>-</span></p>
                    <p><span class="font-medium">Forma de Pago:</span> <span id="payment_method_consult"
                            wire:ignore>-</span>
                    </p>
                    <p><span class="font-medium">Codigo de Manifiesto:</span> <span id="manifest_no_consult"
                            wire:ignore>-</span></p>
                </div>
            </div>
        </div>
    </x-filament::section>

    {{-- Datos de Recibo y Estado de Entrega --}}
    <x-filament::section collapsible>
        <x-slot name="heading">
            Datos de Recibo y Estado de Entrega
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="received_by_name"
                    class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                    Recibido por:<span class="text-danger-600 dark:text-danger-400 font-medium"></span>
                </label>
                <x-filament::input.wrapper>
                    <x-filament::input type="text" name="received_by_name" wire:model="received_by_name"
                        id="received_by_name" value="" />
                </x-filament::input.wrapper>
            </div>
            <div>
                <label for="received_by_document"
                    class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                    DPI de quien recibió:<span class="text-danger-600 dark:text-danger-400 font-medium"></span>
                </label>
                <x-filament::input.wrapper>
                    <x-filament::input type="text" name="received_by_document" wire:model="received_by_document"
                        id="received_by_document" value="" />
                </x-filament::input.wrapper>
            </div>
            <div>
                <label for="guia_madre"
                    class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                    ¿Firmó?<span class="text-danger-600 dark:text-danger-400 font-medium"></span>
                </label>
                <div>
                    <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                        No
                        <button x-data="{
                            state: false,
                            toggle() {
                                this.state = !this.state;
                                this.update();
                            },
                            update() {
                                this.$refs.hiddenInput.value = this.state;
                                window.dispatchEvent(new CustomEvent('toggle-changed', { detail: this.state }));
                            }
                        }" x-init="() => {
                            let self = $data; // referencia al scope Alpine
                            window.addEventListener('set-toggle', e => {
                                self.state = e.detail;
                                self.update();
                            });
                        }" x-bind:aria-checked="state.toString()"
                            x-on:click="toggle()"
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
                                x-bind:class="state ? 'translate-x-5 rtl:-translate-x-5' : 'translate-x-0'">
                            </span>

                            <input type="hidden" name="linkLater" wire:model="signed" x-ref="hiddenInput"
                                :value="state">
                        </button>

                        <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            Sí
                        </span>
                    </label>
                </div>


            </div>
        </div>
        <div class="space-y-2 mt-2">
            <label for="observations" class="text-sm font-medium text-gray-700 dark:text-white">
                Observaciones
            </label>
            <textarea id="observations" wire:model="observations" name="observations" rows="4"
                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900
                placeholder-gray-400 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500
                dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500"
                placeholder="Escribe tus observaciones aquí..."></textarea>
        </div>

    </x-filament::section>

    {{-- Botón de Guardar --}}
    <div>
        <x-filament::grid.column style="--col-span-default: span 5 / span 5;">
            <div class="flex justify-between items-center mb-4">
                {{-- Botón izquierdo --}}
                <div>
                    <x-filament::button type="button" class="saveShipment" wire:click="confirmSave" color="primary">
                        Guardar Información
                    </x-filament::button>
                </div>

                {{-- Botones derechos --}}
                <div class="flex space-x-2">
                    <x-filament::button type="button" class="updateGuideButton" wire:click="openUpdateGuides"
                        color="danger">
                        Modificar Guía
                    </x-filament::button>
                </div>
            </div>
        </x-filament::grid.column>
    </div>

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
                                        wire:click="removeProduct({{ $index }})" label=""
                                        color="danger" />
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

    <script src="{{ asset('js/shipment_delivery.js') }}"></script>
</x-filament-panels::page>
