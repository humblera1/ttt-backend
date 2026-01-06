@php
    /** @var \App\Models\NotificationType|null $record */
    $record = $getRecord();

    $placeholders = $record?->placeholders ?? [];
@endphp

@if (empty($placeholders))
    <p class="text-sm text-gray-500">
        No placeholders are defined for this notification type.
    </p>
@else
    <ul class="space-y-2">
        @foreach ($placeholders as $placeholder)
            <li class="p-2">
                <x-filament::badge
                    color="info"
                    size="md"
                    class="whitespace-nowrap inline-flex w-auto"
                >
                    {{ $placeholder['key'] ?? '' }}
                </x-filament::badge>

                <div class="mt-2 text-sm">
                    {{ $placeholder['label'] ?? '' }}
                </div>

                @if (!empty($placeholder['description']))
                    <div class="text-sm text-gray-600">
                        {{ $placeholder['description'] }}
                    </div>
                @endif
            </li>
        @endforeach
    </ul>
@endif
