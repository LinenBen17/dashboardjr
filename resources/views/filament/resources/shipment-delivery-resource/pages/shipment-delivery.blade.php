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
                    <p><span class="font-medium">No. Manifiesto:</span> <span id="manifest_no_consult"
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
        <x-filament::button type="button" class="saveShipment" wire:click="confirmSave" color="primary">
            Guardar Envío
        </x-filament::button>
    </div>

    <script src="{{ asset('js/shipment_delivery.js') }}"></script>
</x-filament-panels::page>
