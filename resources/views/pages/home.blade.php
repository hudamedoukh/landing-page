@extends('layouts.app', ['title' => 'Home'])

@section('content')
    <section class="flex flex-col items-center gap-6 text-center">
        <h1 class="text-4xl font-bold tracking-tight sm:text-5xl">{{ $headline }}</h1>
        <p class="max-w-2xl text-lg text-gray-600 dark:text-gray-400">{{ $tagline }}</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('contact') }}" class="rounded-lg bg-indigo-600 px-5 py-3 font-semibold text-white transition hover:bg-indigo-500">Get started</a>
            <a href="{{ route('about') }}" class="rounded-lg border border-gray-300 px-5 py-3 font-semibold transition hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-900">Learn more</a>
        </div>
    </section>

    <section class="mt-20 grid gap-6 md:grid-cols-3">
        @foreach ($features as $feature)
            <div class="rounded-xl border border-gray-200 p-6 dark:border-gray-800">
                <h2 class="text-lg font-semibold">{{ $feature['title'] }}</h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $feature['description'] }}</p>
            </div>
        @endforeach
    </section>
@endsection
