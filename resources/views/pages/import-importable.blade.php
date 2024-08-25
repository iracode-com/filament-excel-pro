<x-filament-panels::page>
    {{ $this->table }}

    <form wire:submit="create">
        {{ $this->form }}

        <x-filament::button type="submit" class="mt-3">{{ __('Upload media') }}</x-filament::button>
    </form>

    <x-filament-actions::modals/>
</x-filament-panels::page>
