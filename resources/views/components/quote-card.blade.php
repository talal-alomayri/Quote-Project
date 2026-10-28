@props(['quote', 'owned' => false])

<blockquote
    id="quote-card-{{ $quote->id }}"
    data-quote-body="{{ $quote->body }}"
    data-quote-author="{{ $owned ? auth()->user()->name : $quote->user->name }}"
    class="relative flex h-full flex-col justify-between rounded-2xl bg-quote-card-light p-2 text-quote-text-primary-light transition-colors duration-300 dark:bg-quote-card-dark dark:text-quote-text-primary-dark">

    {{-- Owner-only actions — three-dots dropdown in the top-right corner --}}
    @if ($owned)
        <details data-ui-only class="group absolute right-10 top-2 z-20">
            <summary aria-label="Quote options"
                class="flex h-8 w-8 cursor-pointer list-none items-center justify-center rounded-full text-quote-text-secondary-light transition hover:bg-quote-accent/10 hover:text-quote-accent focus:outline-none focus-visible:ring-2 focus-visible:ring-quote-accent/40 dark:text-quote-text-secondary-dark [&::-webkit-details-marker]:hidden">
                <svg aria-hidden="true" fill="currentColor" viewBox="0 0 24 24" class="h-5 w-5">
                    <circle cx="5" cy="12" r="1.75" />
                    <circle cx="12" cy="12" r="1.75" />
                    <circle cx="19" cy="12" r="1.75" />
                </svg>
            </summary>

            <div
                class="absolute right-0 mt-2 w-40 overflow-hidden rounded-xl border border-quote-border-light bg-quote-card-light py-1 shadow-lg transition-colors duration-300 dark:border-quote-border-dark dark:bg-quote-card-dark">
                <a href="{{ route('home', ['edit' => $quote->id]) }}"
                    class="block px-4 py-2 text-sm font-medium transition hover:bg-quote-accent/10 hover:text-quote-accent">
                    Edit Quote
                </a>

                <form method="POST" action="{{ route('quotes.destroy', $quote) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="block w-full px-4 py-2 text-left text-sm font-medium text-red-600 transition hover:bg-red-500/10 dark:text-red-400">
                        Delete Quote
                    </button>
                </form>
            </div>
        </details>
    @endif

    <div class="absolute right-3 top-3 z-20">
          {{-- Copy quote text to clipboard --}}
                <button
                    type="button"
                    data-copy-quote="{{ $quote->body }}"
                    aria-label="Copy quote to clipboard"
                    title="Copy quote"
                    class="flex items-center gap-1 rounded-full px-2 py-1 text-quote-text-secondary-light transition hover:bg-quote-accent/10 hover:text-quote-accent dark:text-quote-text-secondary-dark"
                >
                    {{-- Clipboard icon --}}
                    <svg aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 block" data-icon-copy>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                    </svg>
                    {{-- Checkmark icon (shown after copy) --}}
                    <svg aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 hidden text-green-500" data-icon-check>
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </button>
    </div>

    {{-- Top: decorative quote mark + quote body — grows to fill the leftover space --}}
    <div>
        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
            class="mb-3 block h-8 w-8 fill-current text-quote-accent transition-colors duration-300 dark:text-quote-text-primary-dark"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path d="M96 280C96 213.7 149.7 160 216 160L224 160C241.7 160 256 174.3 256 192C256 209.7 241.7 224 224 224L216 224C185.1 224 160 249.1 160 280L160 288L224 288C259.3 288 288 316.7 288 352L288 416C288 451.3 259.3 480 224 480L160 480C124.7 480 96 451.3 96 416L96 280zM352 280C352 213.7 405.7 160 472 160L480 160C497.7 160 512 174.3 512 192C512 209.7 497.7 224 480 224L472 224C441.1 224 416 249.1 416 280L416 288L480 288C515.3 288 544 316.7 544 352L544 416C544 451.3 515.3 480 480 480L416 480C380.7 480 352 451.3 352 416L352 280z"/></svg>

        <p class="font-serif text-lg lg:text-2xl leading-relaxed">“ {{ $quote->body }} ”</p>
    </div>

    {{-- Bottom: divider + footer — pinned to the bottom of every card --}}
    <div>
        <hr class="my-4 border-t border-quote-border-light dark:border-quote-border-dark">

        <div class="flex items-center justify-between gap-3">

            {{-- Left: author avatar + name --}}
            <div class="flex min-w-0 items-center gap-2">
                <span
                    class="flex h-7 w-7 lg:h-10 lg:w-10 shrink-0 items-center justify-center rounded-full bg-quote-accent/15 text-quote-accent">
                    <svg aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"
                        class="h-4 w-4 lg:h-6 lg:w-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" />
                    </svg>
                </span>
                <span class="truncate text-sm font-semibold">{{ $owned ? 'You' : $quote->user->name }}</span>
            </div>

            {{-- Right: copy + like icon/count + date --}}
            <div class="flex shrink-0 items-center">

                <span class="text-xs lg:text-sm text-quote-text-secondary-light dark:text-quote-text-secondary-dark">
                    {{ $quote->created_at->format('Y-m-d') }}
                </span>

                {{-- Guests may see the like count but must log in to like. --}}
                @auth
                    @if ($quote->isLikedBy(auth()->user()))
                        <form method="POST" action="{{ route('quotes.unlike', $quote) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" aria-label="Unlike this quote"
                                class="flex items-center gap-1 rounded-full px-2 py-1 text-sm lg:text-lg font-medium text-quote-accent transition hover:bg-quote-accent/10">
                                <svg aria-hidden="true" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                    <path
                                        d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001Z" />
                                </svg>
                                {{ $quote->likers->count() }}
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('quotes.like', $quote) }}">
                            @csrf
                            <button type="submit" aria-label="Like this quote"
                                class="flex items-center gap-1 rounded-full px-2 py-1 text-sm lg:text-lg font-medium text-quote-text-secondary-light transition hover:bg-quote-accent/10 hover:text-quote-accent dark:text-quote-text-secondary-dark">
                                <svg aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                                    stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                </svg>
                                {{ $quote->likers->count() }}
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('login') }}" aria-label="Log in to like this quote"
                        class="rounded-full px-2 py-1 text-sm font-medium text-quote-text-secondary-light transition hover:text-quote-accent dark:text-quote-text-secondary-dark">
                        ♡ {{ $quote->likers->count() }}
                    </a>
                @endauth
            </div>
        </div>
    </div>
</blockquote>
