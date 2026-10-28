@extends('Layout.app')

@section('title', 'Liked Quotes')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8">

        {{-- Page heading --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-quote-text-primary-light dark:text-quote-text-primary-dark">
                Liked Quotes
            </h1>
            <p class="mt-1 text-quote-text-secondary-light dark:text-quote-text-secondary-dark">
                Quotes you have hearted — your personal collection of favourites.
            </p>
        </div>

        {{-- Quotes list --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($quotes as $quote)
                <x-quote-card :quote="$quote" />
            @empty
                <div
                    class="rounded-2xl bg-quote-card-light p-10 text-center shadow transition-colors duration-300 dark:bg-quote-card-dark">
                    <p class="text-quote-text-secondary-light dark:text-quote-text-secondary-dark">
                        You have not liked any quotes yet. Explore the
                        <a href="{{ route('home') }}"
                            class="font-medium text-quote-accent underline-offset-2 hover:underline">home page</a>
                        to find your favourites!
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($quotes->hasPages())
            <div class="mt-8">
                {{ $quotes->links() }}
            </div>
        @endif

    </div>
@endsection
