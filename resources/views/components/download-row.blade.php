@props([
    'title',
    'duration',
    'fetchedAt',
])

<div class="flex flex-col gap-4 border-b border-gray-200 py-5 last:border-b-0 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex min-w-0 flex-1 items-start gap-4">
        <span class="mt-0.5 shrink-0 text-gray-400" aria-hidden="true">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M6.5 4.5v11l9-5.5-9-5.5z" />
            </svg>
        </span>
        <div class="min-w-0">
            <p class="font-semibold text-gray-900">
                Title: {{ $title }}
            </p>
            <p class="mt-1 text-sm text-gray-500">
                Duration: {{ $duration }} Date Fetched: {{ $fetchedAt }}
            </p>
        </div>
    </div>
    <div class="flex shrink-0 flex-wrap items-center gap-2 sm:gap-3">
        <button
            type="button"
            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
        >
            View
        </button>
        <button
            type="button"
            class="inline-flex items-center gap-1.5 rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800"
        >
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M10 2.5a.75.75 0 0 1 .75.75v7.69l2.22-2.22a.75.75 0 1 1 1.06 1.06l-3.5 3.5a.75.75 0 0 1-1.06 0l-3.5-3.5a.75.75 0 1 1 1.06-1.06l2.22 2.22V3.25A.75.75 0 0 1 10 2.5Z" />
                <path d="M3.5 12.75a.75.75 0 0 1 .75.75v2.5h11.5v-2.5a.75.75 0 0 1 1.5 0v3.25a.75.75 0 0 1-.75.75H3.25a.75.75 0 0 1-.75-.75v-3.25a.75.75 0 0 1 .75-.75Z" />
            </svg>
            Download
        </button>
    </div>
</div>
