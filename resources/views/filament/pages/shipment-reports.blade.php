<x-filament::page>
    <div class="space-y-6">
        {{-- 🔹 FILTROS --}}
        <x-filament::section heading="Filtros">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <x-filament::input.wrapper>
                    <x-filament::input type="date" wire:model="from" />
                </x-filament::input.wrapper>

                <x-filament::input.wrapper>
                    <x-filament::input type="date" wire:model="to" />
                </x-filament::input.wrapper>

                <x-filament::input.wrapper>
                    <x-filament::input placeholder="Buscar cliente..." wire:model.defer="cliente" />
                </x-filament::input.wrapper>

            </div>
        </x-filament::section>

        {{-- 🔹 OPCIONES --}}
        <x-filament::section heading="Opciones">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Agrupación --}}
                {{-- <div>
                    <label class="text-sm font-medium">Agrupación</label>
                    <div class="flex gap-4 mt-2">
                        <label class="flex items-center gap-2">
                            <input type="radio" wire:model="agrupacion" value="destino">
                            Destino
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="radio" wire:model="agrupacion" value="origen">
                            Origen
                        </label>
                    </div>
                </div> --}}

                {{-- Tipo --}}
                <div>
                    <label class="text-sm font-medium">Tipo</label>
                    <div class="flex gap-4 mt-2">
                        <label class="flex items-center gap-2">
                            <input type="radio" wire:model="tipo" value="detallado">
                            Detallado
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" wire:model="tipo" value="consolidado">
                            Consolidado
                        </label>
                    </div>
                </div>

                {{-- Localidad --}}
                <div>
                    <label class="text-sm font-medium">Localidad</label>
                    <div class="flex gap-4 mt-2">
                        <label class="flex items-center gap-2">
                            <input type="radio" wire:model="localidad" value="Guatemala">
                            Guatemala
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="radio" wire:model="localidad" value="Departamental">
                            Departamental
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" wire:model="localidad" value="Todo">
                            Todo
                        </label>
                    </div>
                </div>

                {{-- Manifiestos auditable --}}
                <div>
                    <label class="text-sm font-medium">Manifiestos auditable</label>
                    <div class="flex gap-4 mt-2">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" wire:model="manifest_auditable" value="manifest_auditable">
                            Manifiestos Auditables
                        </label>
                    </div>
                </div>

                {{-- Select FOrmas de PAgo --}}
                <div>
                    <label class="text-sm font-medium">Formas de pago</label>
                    <div class="flex gap-4 mt-2">
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
                </div>
                {{-- Contra entrega --}}
                {{-- <div class="md:col-span-2">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="contraEntrega">
                        Solo contra entrega
                    </label>
                </div> --}}

            </div>
        </x-filament::section>

        {{-- 🔹 ACCIONES --}}
        <div class="flex gap-4">

            <x-filament::button icon="heroicon-o-printer" wire:click="verEnPantalla">
                Ver en Pantalla
            </x-filament::button>

            <x-filament::button color="success" icon="heroicon-o-document-arrow-down" wire:click="exportarExcel">
                Exportar Excel
            </x-filament::button>

        </div>

    </div>

    {{-- MODAL PARA VISUALIZAR REPORTE EN PANTALLA --}}
    <x-filament::modal id="reporte-modal" width="sm">
        <div class="space-y-6">
            <h2 class="font-semibold text-center">RESUMEN DE ENVÍOS</h2>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 space-y-3">
                {{-- fila --}}
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Contado</span>
                    <span class="font-semibold">{{ $this->data->contado ?? 0 }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Por cobrar</span>
                    <span class="font-semibold">{{ $this->data->por_cobrar ?? 0 }}</span>
                </div>
                <div class="flex justify-between text-sm pl-4">
                    <span class="text-gray-500">Crédito</span>
                    <span class="font-semibold">{{ $this->data->credito ?? 0 }}</span>
                </div>
                <div class="flex justify-between text-sm pl-4">
                    <span class="text-gray-500">Prepago</span>
                    <span class="font-semibold">{{ $this->data->prepago ?? 0 }}</span>
                </div>
                {{-- divisor --}}
                <div class="border-t pt-3 flex justify-between text-base font-bold">
                    <span>Total</span>
                    <span
                        class="font-bold">{{ ($this->data->contado ?? 0) + ($this->data->por_cobrar ?? 0) + ($this->data->credito ?? 0) + ($this->data->prepago ?? 0) }}</span>
                </div>

            </div>
            <p class="text-sm text-gray-500">Total de envíos: {{ $this->totalRegistros }}</p>
        </div>
    </x-filament::modal>
</x-filament::page>
