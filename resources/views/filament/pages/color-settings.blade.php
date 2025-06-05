<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex gap-3">
            <x-filament::button type="submit" color="primary">
                Зберегти
            </x-filament::button>

            <x-filament::button
                type="button"
                color="gray"
                outlined
                wire:click="resetColors"
            >
                Скинути
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
