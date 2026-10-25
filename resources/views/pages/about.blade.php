@extends('layouts.app', ['title' => __('About')])

@section('content')
    <section class="flex max-w-3xl flex-col gap-4">
        <h1 class="text-4xl font-bold tracking-tight rtl:tracking-normal">{{ __('About Nimbus') }}</h1>
        <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">{{ __('Founded in :year', ['year' => $foundedYear]) }}</p>
        <p class="text-lg text-gray-600 dark:text-gray-400">{{ $mission }}</p>
    </section>

    <section class="mt-16">
        <h2 class="text-2xl font-semibold">{{ __('Our team') }}</h2>
        <div class="mt-6 grid gap-6 sm:grid-cols-3">
            @foreach ($team as $member)
                <div class="rounded-xl border border-gray-200 p-6 text-center dark:border-gray-800">
                    <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-indigo-100 text-xl font-bold text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400">
                        {{ str($member['name'])->explode(' ')->map(fn (string $part) => $part[0])->implode('') }}
                    </div>
                    <h3 class="mt-4 font-semibold">{{ $member['name'] }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $member['role'] }}</p>
                </div>
            @endforeach
        </div>
    </section>
@endsection
