@extends('layouts.app', ['title' => 'Contact us'])

@section('content')
    <section class="flex max-w-3xl flex-col gap-4">
        <h1 class="text-4xl font-bold tracking-tight">Contact us</h1>
        <p class="text-lg text-gray-600 dark:text-gray-400">Have a question or want a demo? We'd love to hear from you.</p>
    </section>

    <dl class="mt-12 grid gap-6 sm:grid-cols-3">
        @foreach ($contactDetails as $label => $value)
            <div class="rounded-xl border border-gray-200 p-6 dark:border-gray-800">
                <dt class="text-sm font-medium text-indigo-600 dark:text-indigo-400">{{ $label }}</dt>
                <dd class="mt-2 font-semibold">{{ $value }}</dd>
            </div>
        @endforeach
    </dl>
@endsection
