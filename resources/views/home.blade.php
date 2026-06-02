@extends('layouts.app')

@section('content')
    <div class="flex min-h-screen flex-col">
        <header class="w-full bg-white py-8 sm:py-10">
            <div class="mx-auto flex max-w-4xl flex-col items-center gap-4 px-4 sm:px-6">
                <img
                    src="{{ asset('images/logo-horizontal.png') }}"
                    alt="VaultFetch — Local Media Downloader"
                    class="h-auto w-full max-w-3xl"
                />
                <form action="{{ route('logout') }}" method="post" class="self-end">
                    @csrf
                    <button
                        type="submit"
                        class="text-sm text-gray-500 transition hover:text-gray-800"
                    >
                        Sign out
                    </button>
                </form>
            </div>
        </header>

        <div class="mx-auto flex w-full max-w-4xl flex-1 flex-col px-4 py-8 sm:px-6 sm:py-12">
            <main class="flex-1">
                <div class="rounded-xl bg-white p-6 shadow-md sm:p-8">
                    @if (session('status'))
                        <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            <ul class="list-inside list-disc space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <section class="mb-10">
                        <h2 class="mb-4 text-lg font-medium text-gray-800">
                            Enter Video URL
                        </h2>
                        <form
                            id="fetch-form"
                            action="{{ route('fetch.store') }}"
                            method="post"
                            class="flex flex-col gap-3 sm:flex-row"
                        >
                            @csrf
                            <input
                                id="fetch-url"
                                type="url"
                                name="url"
                                value="{{ old('url') }}"
                                placeholder="https://www.youtube.com/watch?v=..."
                                required
                                class="min-w-0 flex-1 rounded-md border border-gray-300 px-4 py-2.5 text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 read-only:cursor-not-allowed read-only:bg-gray-50"
                            />
                            <button
                                id="fetch-submit"
                                type="submit"
                                class="inline-flex min-w-[7rem] shrink-0 items-center justify-center gap-2 rounded-md bg-blue-600 px-6 py-2.5 font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60 sm:px-8"
                            >
                                <span id="fetch-submit-label">Fetch</span>
                                <span id="fetch-submit-loading" class="hidden items-center gap-2">
                                    <svg
                                        class="h-4 w-4 animate-spin"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        aria-hidden="true"
                                    >
                                        <circle
                                            class="opacity-25"
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke="currentColor"
                                            stroke-width="4"
                                        />
                                        <path
                                            class="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                        />
                                    </svg>
                                    Fetching…
                                </span>
                            </button>
                        </form>
                    </section>

                    <section>
                        <h2 class="text-lg font-medium text-gray-800">
                            Recent Downloads
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Last {{ config('vaultfetch.recent_limit') }} videos fetched
                        </p>

                        <div class="mt-4 space-y-4">
                            @forelse ($recentDownloads as $download)
                                <x-download-row :download="$download" />
                            @empty
                                <p class="py-8 text-center text-sm text-gray-500">
                                    No downloads yet. Paste a URL above to fetch your first video.
                                </p>
                            @endforelse
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </div>
@endsection
