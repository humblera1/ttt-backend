@php
    /** @var \App\Models\NotificationType|null $record */
    $record = $getRecord();

    $notificationData = $record?->data ?? [];
@endphp

@if (empty($notificationData))
    <p class="text-sm text-gray-500">
        There is no additional data for this notification type.
    </p>
@else
    <ul>
        @foreach ($notificationData as $key => $value)
            <li class="mb-4">
                <x-filament::badge
                    color="info"
                    size="md"
                    class="whitespace-nowrap inline-flex w-auto"
                >
                    {{ $key }}
                </x-filament::badge>

                <div class="mt-2 text-sm">
                    {{ $value }}
                </div>
            </li>
        @endforeach
    </ul>
@endif
