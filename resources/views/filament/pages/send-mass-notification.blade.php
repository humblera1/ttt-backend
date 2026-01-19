<x-filament::page>
    <form wire:submit.prevent="save" class="space-y-4">
        {{ $this->form }}
        <div class="flex justify-end">
            <x-filament::button type="submit">
                Send
            </x-filament::button>
        </div>
    </form>
</x-filament::page>
