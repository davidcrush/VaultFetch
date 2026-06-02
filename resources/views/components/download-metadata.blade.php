@props([
    'download',
])

<div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
    <div class="rounded-lg border border-gray-100 bg-gray-50 px-4 py-3">
        <div class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-gray-500">
            <svg class="h-4 w-4 shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-13a.75.75 0 0 0-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 0 0 0-1.5h-3.25V5Z" clip-rule="evenodd" />
            </svg>
            {{ __('vaultfetch.metadata.duration') }}
        </div>
        <p class="mt-1.5 text-sm font-medium text-gray-900">
            {{ $download->humanDuration() }}
        </p>
    </div>

    <div class="rounded-lg border border-gray-100 bg-gray-50 px-4 py-3">
        <div class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-gray-500">
            <svg class="h-4 w-4 shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 17 6.75v10.5A2.75 2.75 0 0 1 14.25 20H5.75A2.75 2.75 0 0 1 3 17.25V6.75A2.75 2.75 0 0 1 5.75 4H6V2.75A.75.75 0 0 1 5.75 2Zm-1.5 5.5c0-.69.56-1.25 1.25-1.25h10.5c.69 0 1.25.56 1.25 1.25v8.5c0 .69-.56 1.25-1.25 1.25H5.75c-.69 0-1.25-.56-1.25-1.25v-8.5Z" clip-rule="evenodd" />
            </svg>
            {{ __('vaultfetch.metadata.added') }}
        </div>
        <p class="mt-1.5 text-sm font-medium text-gray-900">
            <time
                datetime="{{ $download->addedAt()->toIso8601String() }}"
                data-relative-time
            >
                {{ $download->formattedAddedAt() }}
            </time>
        </p>
    </div>

    @if ($download->expiresAt())
        <div class="rounded-lg border border-gray-100 bg-gray-50 px-4 py-3">
            <div class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-gray-500">
                <svg class="h-4 w-4 shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 17 6.75v10.5A2.75 2.75 0 0 1 14.25 20H5.75A2.75 2.75 0 0 1 3 17.25V6.75A2.75 2.75 0 0 1 5.75 4H6V2.75A.75.75 0 0 1 5.75 2Zm-1.5 5.5v8.5h10.5v-8.5H4.25Z" clip-rule="evenodd" />
                </svg>
                {{ __('vaultfetch.metadata.expires') }}
            </div>
            <p @class([
                'mt-1.5 text-sm font-medium',
                'text-amber-800' => $download->isExpired(),
                'text-gray-900' => ! $download->isExpired(),
            ])>
                <time
                    datetime="{{ $download->expiresAt()->toIso8601String() }}"
                    data-relative-time
                >
                    {{ $download->formattedExpiresAt() }}
                </time>
            </p>
        </div>
    @endif
</div>

@if ($download->isExpired())
    <div class="mt-3">
        <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-800">
            {{ __('vaultfetch.metadata.expired') }}
        </span>
    </div>
@elseif ($download->status->value !== 'completed')
    <div class="mt-3">
        <span class="inline-flex items-center rounded-full bg-gray-200 px-2.5 py-1 text-xs font-medium text-gray-700">
            {{ __('vaultfetch.status.'.$download->status->value) }}
        </span>
    </div>
@endif
