@extends('layouts.app')

@section('content')
    <div class="flex min-h-screen flex-col">
        <header class="w-full bg-white py-8 sm:py-10">
            <div class="mx-auto flex max-w-4xl justify-center px-4 sm:px-6">
                <img
                    src="{{ asset('images/logo-horizontal.png') }}"
                    alt="{{ __('vaultfetch.app.logo_alt') }}"
                    class="h-auto w-full max-w-3xl"
                />
            </div>
        </header>

        <div class="mx-auto flex w-full max-w-4xl flex-1 flex-col px-4 py-8 sm:px-6 sm:py-12">
            <main class="flex-1">
                <div class="mx-auto max-w-md rounded-xl bg-white p-6 shadow-md sm:p-8">
                    <h2 class="mb-6 text-center text-lg font-medium text-gray-800">
                        {{ __('vaultfetch.auth.sign_in') }}
                    </h2>

                    @if ($errors->any())
                        <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            <ul class="list-inside list-disc space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="post" class="space-y-4">
                        @csrf

                        <div>
                            <label for="email" class="mb-1 block text-sm font-medium text-gray-700">
                                {{ __('vaultfetch.auth.email') }}
                            </label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="email"
                                class="w-full rounded-md border border-gray-300 px-4 py-2.5 text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                            />
                        </div>

                        <div>
                            <label for="password" class="mb-1 block text-sm font-medium text-gray-700">
                                {{ __('vaultfetch.auth.password') }}
                            </label>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                class="w-full rounded-md border border-gray-300 px-4 py-2.5 text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                            />
                        </div>

                        <div class="flex items-center gap-2">
                            <input
                                id="remember"
                                type="checkbox"
                                name="remember"
                                value="1"
                                @checked(old('remember'))
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            />
                            <label for="remember" class="text-sm text-gray-600">
                                {{ __('vaultfetch.auth.remember_me') }}
                            </label>
                        </div>

                        <button
                            type="submit"
                            class="w-full rounded-md bg-blue-600 px-6 py-2.5 font-medium text-white transition hover:bg-blue-700"
                        >
                            {{ __('vaultfetch.auth.sign_in') }}
                        </button>
                    </form>
                </div>
            </main>
        </div>
    </div>
@endsection
