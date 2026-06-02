@extends('layouts.app')

@section('content')
    <div class="flex min-h-screen flex-col">
        <header class="w-full bg-white py-4 shadow-sm">
            <div class="mx-auto flex max-w-4xl items-center gap-4 px-4 sm:px-6">
                <a
                    href="{{ route('home') }}"
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                >
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M11.78 3.22a.75.75 0 0 1 0 1.06L7.06 8h7.69a.75.75 0 0 1 0 1.5H7.06l4.72 4.72a.75.75 0 1 1-1.06 1.06l-5.5-5.5a.75.75 0 0 1 0-1.06l5.5-5.5a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                    </svg>
                    {{ __('vaultfetch.downloads.back') }}
                </a>
                <h1 class="min-w-0 flex-1 truncate text-lg font-medium text-gray-900">
                    {{ $download->title }}
                </h1>
                @if ($download->canRefetch())
                    <form action="{{ route('downloads.refetch', $download) }}" method="post">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 0 1-9.201 2.466l-.312-.311h2.433a.75.75 0 0 0 0-1.5H4.979a.75.75 0 0 0-.75.75v3.242a.75.75 0 0 0 1.5 0v-2.43l.31.31a7 7 0 0 0 11.712-3.138.75.75 0 0 0-1.449-.39Zm-9.864-2.47a5.5 5.5 0 0 1 9.201-2.47l.312.311H12.53a.75.75 0 0 0 0 1.5h3.243a.75.75 0 0 0 .75-.75V4.378a.75.75 0 0 0-1.5 0v2.43l-.31-.31A7 7 0 0 0 3.31 8.21a.75.75 0 1 0 1.438.744Z" clip-rule="evenodd" />
                            </svg>
                            {{ __('vaultfetch.downloads.refetch') }}
                        </button>
                    </form>
                @else
                    <a
                        href="{{ route('downloads.file', $download) }}"
                        class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 2.5a.75.75 0 0 1 .75.75v7.69l2.22-2.22a.75.75 0 1 1 1.06 1.06l-3.5 3.5a.75.75 0 0 1-1.06 0l-3.5-3.5a.75.75 0 1 1 1.06-1.06l2.22 2.22V3.25A.75.75 0 0 1 10 2.5Z" />
                            <path d="M3.5 12.75a.75.75 0 0 1 .75.75v2.5h11.5v-2.5a.75.75 0 0 1 1.5 0v3.25a.75.75 0 0 1-.75.75H3.25a.75.75 0 0 1-.75-.75v-3.25a.75.75 0 0 1 .75-.75Z" />
                        </svg>
                        {{ __('vaultfetch.downloads.download') }}
                    </a>
                @endif
            </div>
        </header>

        <div class="mx-auto w-full max-w-4xl flex-1 px-4 py-6 sm:px-6 sm:py-8">
            <div class="mb-6 rounded-xl bg-white p-4 shadow-sm sm:p-5">
                <x-download-metadata :download="$download" />
            </div>
            <div class="overflow-hidden rounded-xl bg-black shadow-md">
                <video
                    class="aspect-video w-full"
                    controls
                    playsinline
                    preload="metadata"
                    src="{{ route('downloads.stream', $download) }}"
                >
                    {{ __('vaultfetch.downloads.unsupported_browser') }}
                </video>
            </div>
        </div>
    </div>
@endsection
