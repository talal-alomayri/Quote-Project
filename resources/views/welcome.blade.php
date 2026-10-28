@extends('Layout.app')

@section('title', 'Home')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Quote of the day: مميز وبارز بشكل احترافي --}}
        @if ($quoteOfTheDay)
            <section class="mb-12">
                <div class="mb-8 flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-2xl font-extrabold tracking-tight text-quote-text-primary-light dark:text-quote-text-primary-dark sm:text-3xl">
                        Quote of the Day
                    </h2>
                    <span class="text-xs font-semibold uppercase tracking-wider text-quote-text-secondary-light dark:text-quote-text-secondary-dark">
                        {{ now()->format('F j, Y') }}
                    </span>
                </div>

                {{-- كارد اليوم الكبير — له تصميم خاص مستقل عن كاردات بقية الاقتباسات --}}
                <x-quote-of-the-day-card :quote="$quoteOfTheDay" />
            </section>
        @endif

        {{-- عنوان قسم بقية الاقتباسات --}}
        <div class="mb-6 flex items-center justify-between">
            <h3 class="text-xl font-bold tracking-tight text-quote-text-primary-light dark:text-quote-text-primary-dark sm:text-2xl">
                Quotes
            </h3>
        </div>

        @error('body')
            <p class="mb-6 rounded-2xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm font-medium text-red-600 dark:text-red-400"
                role="alert">{{ $message }}</p>
        @enderror

        {{-- شبكة الاقتباسات بـ Grid متناسق وثابت الارتفاع --}}
        <section class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">

            {{-- The signed-in user's own quotes --}}
            @auth
                @foreach ($myQuotes as $quote)
                    @if (request('edit') == $quote->id)
                        <form method="POST" action="{{ route('quotes.update', $quote) }}"
                            class="flex flex-col justify-between rounded-2xl border border-quote-border-light bg-quote-card-light p-6 shadow-sm dark:border-quote-border-dark dark:bg-quote-card-dark">
                            @csrf
                            @method('PUT')

                            <textarea name="body" rows="3" required minlength="3" maxlength="155" 
                                class="w-full resize-none rounded-xl border border-quote-border-light bg-quote-bg-light p-3 font-serif text-quote-text-primary-light focus:border-quote-accent focus:ring focus:ring-quote-accent/30 dark:border-quote-border-dark dark:bg-quote-bg-dark dark:text-quote-text-primary-dark"
                                autofocus>{{ old('body', $quote->body) }}</textarea>

                            <div class="mt-4 flex items-center gap-3">
                                <button type="submit" class="rounded-full bg-quote-accent px-5 py-2 text-sm font-medium text-white transition hover:bg-quote-accent-hover">Save</button>
                                <a href="{{ route('home') }}" class="text-sm font-medium text-quote-text-secondary-light hover:underline dark:text-quote-text-secondary-dark">Cancel</a>
                            </div>
                        </form>
                    @else
                        <div class="flex flex-col justify-between rounded-2xl border border-quote-border-light bg-quote-card-light p-6 shadow-sm transition-colors duration-300 dark:border-quote-border-dark dark:bg-quote-card-dark">
                            <x-quote-card :quote="$quote" :owned="true" />
                        </div>
                    @endif
                @endforeach
            @endauth

            {{-- Everybody else's quotes --}}
            @foreach ($otherQuotes as $quote)
                <div class="flex flex-col justify-between rounded-2xl border border-quote-border-light bg-quote-card-light p-6 shadow-sm transition-colors duration-300 dark:border-quote-border-dark dark:bg-quote-card-dark">
                    <x-quote-card :quote="$quote" />
                </div>
            @endforeach

            @if ($myQuotes->isEmpty() && $otherQuotes->isEmpty())
                <div class="col-span-full py-12 text-center">
                    <p class="text-quote-text-secondary-light dark:text-quote-text-secondary-dark text-base">
                        No quotes yet. Be the first to share one!
                    </p>
                </div>
            @endif
        </section>
    </div>

    @auth
        {{-- Floating "+" button — authenticated users only --}}
        <button type="button" popovertarget="quote-popup" aria-label="Share a quote"
            class="fixed bottom-8 right-8 z-40 flex h-16 w-16 items-center justify-center rounded-full bg-quote-accent text-white shadow-xl transition duration-300 hover:scale-110 hover:bg-quote-accent-hover focus:outline-none focus-visible:ring-4 focus-visible:ring-quote-accent/40">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"
                class="h-8 w-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
        </button>

        {{-- Pop-up form --}}
        <div id="quote-popup" popover
            class="m-auto w-[90%] max-w-lg rounded-3xl bg-quote-card-light p-0 shadow-2xl backdrop:bg-black/40 backdrop:backdrop-blur-sm dark:bg-quote-card-dark">

            <form method="POST" action="{{ route('quotes.store') }}" class="space-y-4 p-6 sm:p-8">
                @csrf

                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-quote-text-primary-light dark:text-quote-text-primary-dark sm:text-2xl">Share a quote</h2>
                    <button type="button" popovertarget="quote-popup" popovertargetaction="hide" aria-label="Close"
                        class="rounded-full p-2 text-quote-text-secondary-light transition duration-300 hover:bg-quote-accent/10 hover:text-quote-accent dark:text-quote-text-secondary-dark">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div>
                    <textarea id="quote-body" name="body" rows="5" required minlength="3" maxlength="155" autofocus
                        placeholder="Write your quote…"
                        class="peer w-full resize-none rounded-xl border border-quote-border-light bg-quote-bg-light px-4 py-3 text-quote-text-primary-light placeholder-quote-text-secondary-light transition-colors duration-300 focus:border-quote-accent focus:ring focus:ring-quote-accent/30 user-invalid:border-red-500 dark:border-quote-border-dark dark:bg-quote-bg-dark dark:text-quote-text-primary-dark dark:placeholder-quote-text-secondary-dark">{{ old('body') }}</textarea>
                    <p class="mt-2 text-xs text-quote-text-secondary-light peer-user-invalid:text-red-600 dark:text-quote-text-secondary-dark">
                        3–155 characters.</p>
                </div>

                <button type="submit"
                    class="w-full rounded-2xl bg-quote-accent px-6 py-3 font-medium text-white shadow-md transition-colors duration-300 hover:bg-quote-accent-hover focus:outline-none focus-visible:ring-4 focus-visible:ring-quote-accent/40">
                    Send
                </button>
            </form>
        </div>
    @endauth
@endsection