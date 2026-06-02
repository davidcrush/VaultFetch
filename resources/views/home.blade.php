@extends('layouts.app')

@section('content')
    <div class="flex min-h-screen flex-col">
        <header class="w-full bg-white py-8 sm:py-10">
            <div class="mx-auto flex max-w-4xl justify-center px-4 sm:px-6">
                <img
                    src="{{ asset('images/logo-horizontal.png') }}"
                    alt="VaultFetch — Local Media Downloader"
                    class="h-auto w-full max-w-3xl"
                />
            </div>
        </header>

        <div class="mx-auto flex w-full max-w-4xl flex-1 flex-col px-4 py-8 sm:px-6 sm:py-12">
        <main class="flex-1">
            <div class="rounded-xl bg-white p-6 shadow-md sm:p-8">
                <section class="mb-10">
                    <h2 class="mb-4 text-lg font-medium text-gray-800">
                        Enter Video URL
                    </h2>
                    <form action="#" method="post" class="flex flex-col gap-3 sm:flex-row" onsubmit="return false;">
                        <input
                            type="url"
                            name="url"
                            placeholder="https://www.youtube.com/watch?v=..."
                            class="min-w-0 flex-1 rounded-md border border-gray-300 px-4 py-2.5 text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                        />
                        <button
                            type="submit"
                            class="shrink-0 rounded-md bg-blue-600 px-6 py-2.5 font-medium text-white transition hover:bg-blue-700 sm:px-8"
                        >
                            Fetch
                        </button>
                    </form>
                </section>

                <section>
                    <h2 class="text-lg font-medium text-gray-800">
                        Recent Downloads
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Last 10 videos fetched
                    </p>

                    <div class="mt-4">
                        @foreach ($recentDownloads as $download)
                            <x-download-row
                                :title="$download['title']"
                                :duration="$download['duration']"
                                :fetched-at="$download['fetchedAt']"
                            />
                        @endforeach
                    </div>
                </section>
            </div>
        </main>

        </div>
    </div>
@endsection
