@props([
    'download',
])

<article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="flex min-w-0 flex-1 gap-4">
            <span class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-400" aria-hidden="true">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6.5 4.5v11l9-5.5-9-5.5z" />
                </svg>
            </span>
            <div class="min-w-0 flex-1">
                <h3 class="text-base font-semibold leading-snug text-gray-900 sm:text-lg">
                    {{ $download->title ?? __('vaultfetch.downloads.processing') }}
                </h3>
                <x-download-metadata :download="$download" />
            </div>
        </div>

        <div class="flex shrink-0 flex-wrap items-center gap-2 border-t border-gray-100 pt-4 lg:border-t-0 lg:pt-0">
            @if ($download->canRefetch())
                <span class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-500">
                    {{ __('vaultfetch.downloads.view') }}
                </span>
                <form action="{{ route('downloads.refetch', $download) }}" method="post">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 0 1-9.201 2.466l-.312-.311h2.433a.75.75 0 0 0 0-1.5H4.979a.75.75 0 0 0-.75.75v3.242a.75.75 0 0 0 1.5 0v-2.43l.31.31a7 7 0 0 0 11.712-3.138.75.75 0 0 0-1.449-.39Zm-9.864-2.47a5.5 5.5 0 0 1 9.201-2.47l.312.311H12.53a.75.75 0 0 0 0 1.5h3.243a.75.75 0 0 0 .75-.75V4.378a.75.75 0 0 0-1.5 0v2.43l-.31-.31A7 7 0 0 0 3.31 8.21a.75.75 0 1 0 1.438.744Z" clip-rule="evenodd" />
                        </svg>
                        {{ __('vaultfetch.downloads.refetch') }}
                    </button>
                </form>
            @elseif ($download->isFileAvailable())
                <a
                    href="{{ route('downloads.show', $download) }}"
                    class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
                >
                    {{ __('vaultfetch.downloads.view') }}
                </a>
                <a
                    href="{{ route('downloads.file', $download) }}"
                    class="inline-flex items-center gap-1.5 rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800"
                >
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M10 2.5a.75.75 0 0 1 .75.75v7.69l2.22-2.22a.75.75 0 1 1 1.06 1.06l-3.5 3.5a.75.75 0 0 1-1.06 0l-3.5-3.5a.75.75 0 1 1 1.06-1.06l2.22 2.22V3.25A.75.75 0 0 1 10 2.5Z" />
                        <path d="M3.5 12.75a.75.75 0 0 1 .75.75v2.5h11.5v-2.5a.75.75 0 0 1 1.5 0v3.25a.75.75 0 0 1-.75.75H3.25a.75.75 0 0 1-.75-.75v-3.25a.75.75 0 0 1 .75-.75Z" />
                    </svg>
                    {{ __('vaultfetch.downloads.download') }}
                </a>
            @else
                <span class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-500">
                    {{ __('vaultfetch.downloads.view') }}
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-500">
                    {{ __('vaultfetch.downloads.download') }}
                </span>
            @endif
        </div>
    </div>
</article>
