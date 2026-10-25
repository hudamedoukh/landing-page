@php
    $inputClasses = 'mt-2 block w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100';
@endphp

@extends('layouts.app', ['title' => __('Contact us')])

@section('content')
    <section class="flex max-w-3xl flex-col gap-4">
        <h1 class="text-4xl font-bold tracking-tight rtl:tracking-normal">{{ __('Contact us') }}</h1>
        <p class="text-lg text-gray-600 dark:text-gray-400">{{ __("Have a question or want a demo? We'd love to hear from you.") }}</p>
    </section>

    <dl class="mt-12 grid gap-6 sm:grid-cols-3">
        @foreach ($contactDetails as $contactDetail)
            <div class="rounded-xl border border-gray-200 p-6 dark:border-gray-800">
                <dt class="text-sm font-medium text-indigo-600 dark:text-indigo-400">{{ $contactDetail['label'] }}</dt>
                <dd class="mt-2 font-semibold"><bdi @if ($contactDetail['isLeftToRight']) dir="ltr" @endif>{{ $contactDetail['value'] }}</bdi></dd>
            </div>
        @endforeach
    </dl>

    <section class="mt-16 max-w-2xl">
        <h2 class="text-2xl font-bold tracking-tight rtl:tracking-normal">{{ __('Send us a message') }}</h2>

        @if (session('status'))
            <div role="status" class="mt-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 dark:border-green-900 dark:bg-green-950 dark:text-green-300">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('contact.store') }}" class="mt-6 flex flex-col gap-6" novalidate>
            @csrf

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="name" class="text-sm font-medium">{{ __('Name') }}</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="255" autocomplete="name" class="{{ $inputClasses }}">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="text-sm font-medium">{{ __('Email') }}</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" dir="ltr" required maxlength="255" autocomplete="email" class="{{ $inputClasses }}">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="subject" class="text-sm font-medium">{{ __('Subject') }} <span class="text-gray-500 dark:text-gray-400">{{ __('(optional)') }}</span></label>
                <input type="text" id="subject" name="subject" value="{{ old('subject') }}" maxlength="255" class="{{ $inputClasses }}">
                @error('subject')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="message" class="text-sm font-medium">{{ __('Message') }}</label>
                <textarea id="message" name="message" rows="6" required maxlength="5000" class="{{ $inputClasses }}">{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-3 font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-950">
                    {{ __('Send message') }}
                </button>
            </div>
        </form>
    </section>
@endsection
