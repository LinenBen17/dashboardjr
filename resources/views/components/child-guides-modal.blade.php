{{-- MODAL GUIAS HIJAS --}}
<x-filament::modal :id="$id" width="4xl">
    <x-slot name="heading">
        {{ $heading }}
    </x-slot>

    {{-- INPUT Y CONTADOR --}}
    <x-filament::grid class="gap-4" style="--cols-default: repeat(2, minmax(0, 1fr));">
        <x-filament::grid.column style="--col-span-default: span 1 / span 1;">
            <div>
                <label for="child"
                    class="fi-fo-field-wrp-label mb-2 inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                    {{ $label }}
                </label>

                <x-filament::input.wrapper>
                    <x-filament::input type="text" id="child" name="child" :placeholder="$placeholder" />
                </x-filament::input.wrapper>

                <p class="fi-fo-field-wrp-helper-text mt-2 text-sm text-gray-500 dark:text-gray-400">
                    {{ $helperText }}
                </p>
            </div>
        </x-filament::grid.column>

        <x-filament::grid.column style="--col-span-default: span 1 / span 1;">
            <div class="flex flex-col">
                <span class="text-white text-sm font-medium">
                    {{ $counterLabel }}
                </span>

                <span id="guia-hija-count" class="text-white" style="font-size: 24pt">
                    {{ $count }}
                </span>
            </div>
        </x-filament::grid.column>
    </x-filament::grid>

    {{-- TOGGLE --}}
    <div>
        <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
            <button x-data="{
                state: @js($linkLater),
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
                    x-bind:class="state ? 'translate-x-5 rtl:-translate-x-5' : 'translate-x-0'">
                </span>

                {{-- hidden input --}}
                <input type="hidden" name="linkLater" x-bind:value="state">
            </button>

            <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                {{ $toggleLabel }}
            </span>
        </label>
    </div>

    {{-- BOTÓN --}}
    <div>
        <x-filament::button type="button" class="saveChilds" wire:click="{{ $action }}" color="primary">
            {{ $buttonText }}
        </x-filament::button>
    </div>
</x-filament::modal>
