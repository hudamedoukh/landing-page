@php
    $menuItems = [
        'home' => __('Home'),
        'about' => __('About'),
        'contact' => __('Contact us'),
    ];
    $supportedLocales = config('app.supported_locales');
    $currentLocale = app()->getLocale();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $currentLocale) }}" dir="{{ $supportedLocales[$currentLocale]['direction'] ?? 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ?? 'Nimbus' }} · Nimbus</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="flex min-h-screen flex-col bg-white text-gray-900 antialiased dark:bg-gray-950 dark:text-gray-100">
        <header class="border-b border-gray-200 dark:border-gray-800">
            <nav class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-4 px-6 py-4">
                <a href="{{ route('home') }}" class="text-xl font-bold text-indigo-600 dark:text-indigo-400">Nimbus</a>

                <div class="flex flex-wrap items-center gap-6">
                    <ul class="flex gap-6 text-sm font-medium">
                        @foreach ($menuItems as $routeName => $label)
                            <li>
                                <a
                                    href="{{ route($routeName) }}"
                                    @class([
                                        'transition hover:text-indigo-600 dark:hover:text-indigo-400',
                                        'text-indigo-600 dark:text-indigo-400' => request()->routeIs($routeName),
                                        'text-gray-600 dark:text-gray-300' => ! request()->routeIs($routeName),
                                    ])
                                >{{ $label }}</a>
                            </li>
                        @endforeach
                    </ul>

                    <ul class="flex gap-1 rounded-lg border border-gray-200 p-1 text-sm font-medium dark:border-gray-800" aria-label="{{ __('Language') }}">
                        @foreach ($supportedLocales as $locale => $localeDetails)
                            <li>
                                <a
                                    href="{{ route('locale.update', $locale) }}"
                                    lang="{{ $locale }}"
                                    hreflang="{{ $locale }}"
                                    @if ($locale === $currentLocale) aria-current="true" @endif
                                    @class([
                                        'block rounded-md px-3 py-1 transition',
                                        'bg-indigo-600 text-white' => $locale === $currentLocale,
                                        'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-900' => $locale !== $currentLocale,
                                    ])
                                >{{ $localeDetails['name'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </nav>
        </header>

        <main class="mx-auto w-full max-w-5xl grow px-6 py-16">
            @yield('content')
        </main>

        <footer class="border-t border-gray-200 py-6 text-center text-sm text-gray-500 dark:border-gray-800 dark:text-gray-400">
            {{ __('© :year Nimbus. All rights reserved.', ['year' => now()->year]) }}
        </footer>
    </body>
</html>
