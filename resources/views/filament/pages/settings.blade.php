<x-filament::page>
    <form wire:submit.prevent="save" class="space-y-4">
        {{ $this->form }}
        <x-filament::button type="submit">
            Сохранить
        </x-filament::button>
    </form>
</x-filament::page>
