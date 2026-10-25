@php
    $menuItems = [
        'home' => 'Home',
        'about' => 'About',
        'contact' => 'Contact us',
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
            </nav>
        </header>

        <main class="mx-auto w-full max-w-5xl grow px-6 py-16">
            @yield('content')
        </main>

        <footer class="border-t border-gray-200 py-6 text-center text-sm text-gray-500 dark:border-gray-800 dark:text-gray-400">
            &copy; {{ now()->year }} Nimbus. All rights reserved.
        </footer>
    </body>
</html>
