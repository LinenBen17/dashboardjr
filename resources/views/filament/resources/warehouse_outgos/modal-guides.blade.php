<link rel="stylesheet" href="{{ asset('css/warehouseIncomes.css') }}">
<div>
    {{-- <ul id="guide-list" class="space-y-2">
        @foreach ($scannedGuides as $guide)
            <li wire:key="guide-{{ $guide }}"
                class="guide-item flex items-center justify-between px-4 py-2 rounded">
                <span class="guide-number font-mono">{{ $guide }}</span>
                <button wire:click="removeGuide('{{ $guide }}')" class="text-red-600 hover:text-red-800 font-bold">
                    ✕
                </button>
            </li>
        @endforeach
    </ul> --}}
    <div class="guides-container grid grid-cols-2 gap-6 p-4">
        <!-- Guías Madres -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
            <h2 class="text-lg font-semibold text-center text-gray-800 dark:text-gray-200 mb-2">Guías Madres</h2>

            <div class="space-y-2">
                @foreach ($motherGuides as $index => $guide)
                    <div class="flex items-center justify-between bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded-md">
                        <span class="text-gray-800 dark:text-gray-100">{{ $guide }}</span>
                        <button wire:click.prevent="removeMotherGuide({{ $index }})" title="Eliminar">
                            <x-filament::icon icon="heroicon-m-trash"
                                class="h-5 w-5 text-red-600 hover:text-red-800 transition" />
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Guías Hijas -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
            <h2 class="text-lg font-semibold text-center text-gray-800 dark:text-gray-200 mb-2">Guías Hijas</h2>

            <div class="space-y-2">
                @foreach ($childGuides as $index => $guide)
                    <div class="flex items-center justify-between bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded-md">
                        <span class="text-gray-800 dark:text-gray-100">{{ $guide }}</span>
                        <button type="button" wire:click.prevent="removeChildGuide({{ $index }})"
                            title="Eliminar">
                            <x-filament::icon icon="heroicon-m-trash"
                                class="h-5 w-5 text-red-600 hover:text-red-800 transition" />
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>


</div>
