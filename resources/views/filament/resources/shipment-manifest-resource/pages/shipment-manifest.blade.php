<x-filament-panels::page>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
    <!-- DataTables KeyTable -->
    <script src="https://cdn.datatables.net/keytable/2.11.0/js/dataTables.keyTable.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/keytable/2.11.0/css/keyTable.dataTables.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">
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
                <x-filament::button type="button" class="unlinkGuides" wire:click="unlinkGuides" color="danger">
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

    <script src="{{ asset('js/shipment_manifest.js') }}"></script>
</x-filament-panels::page>
