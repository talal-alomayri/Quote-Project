@props(['quote'])

{{-- Hero variant of the quote card, used only for the "Quote of the Day". --}}
<blockquote
    class="relative rounded-3xl border border-quote-border-light bg-quote-card-light p-6 py-12  text-quote-text-primary-light transition-colors duration-300  dark:border-quote-border-dark dark:bg-quote-card-dark dark:text-quote-text-primary-dark">

    {{-- Decorative opening quote mark — top-left --}}
    <svg aria-hidden="true" viewBox="0 0 640 640"
        class="pointer-events-none absolute left-6 -top-10 fill-current text-quote-text-primary-light dark:text-quote-text-primary-dark h-20 w-20">
        <path
            d="M96 280C96 213.7 149.7 160 216 160L224 160C241.7 160 256 174.3 256 192C256 209.7 241.7 224 224 224L216 224C185.1 224 160 249.1 160 280L160 288L224 288C259.3 288 288 316.7 288 352L288 416C288 451.3 259.3 480 224 480L160 480C124.7 480 96 451.3 96 416L96 280zM352 280C352 213.7 405.7 160 472 160L480 160C497.7 160 512 174.3 512 192C512 209.7 497.7 224 480 224L472 224C441.1 224 416 249.1 416 280L416 288L480 288C515.3 288 544 316.7 544 352L544 416C544 451.3 515.3 480 480 480L416 480C380.7 480 352 451.3 352 416L352 280z" />
    </svg>

    {{-- Decorative closing quote mark — bottom-right (same glyph, rotated 180°) --}}
    <svg aria-hidden="true" viewBox="0 0 640 640"
        class="pointer-events-none absolute -bottom-10 right-6 rotate-180 fill-current text-quote-text-primary-light dark:text-quote-text-primary-dark h-20 w-20">
        <path
            d="M96 280C96 213.7 149.7 160 216 160L224 160C241.7 160 256 174.3 256 192C256 209.7 241.7 224 224 224L216 224C185.1 224 160 249.1 160 280L160 288L224 288C259.3 288 288 316.7 288 352L288 416C288 451.3 259.3 480 224 480L160 480C124.7 480 96 451.3 96 416L96 280zM352 280C352 213.7 405.7 160 472 160L480 160C497.7 160 512 174.3 512 192C512 209.7 497.7 224 480 224L472 224C441.1 224 416 249.1 416 280L416 288L480 288C515.3 288 544 316.7 544 352L544 416C544 451.3 515.3 480 480 480L416 480C380.7 480 352 451.3 352 416L352 280z" />
    </svg>

    <div class="relative">
        {{-- Indented so the hero text clears the decorative mark in the corner. --}}
        <p class="pl-5 py-6 font-serif text-2xl leading-relaxed  md:text-3xl">{{ $quote->body }}</p>

        <hr class="my-6 border-t border-quote-border-light dark:border-quote-border-dark">

        <div class="flex flex-wrap items-center justify-between gap-3">
            {{-- Left: author avatar + name --}}
            <div class="flex items-center gap-2">
                <span
                    class="flex h-7 w-7 lg:h-10 lg:w-10 shrink-0 items-center justify-center rounded-full bg-quote-accent/15 text-quote-accent">
                    <svg aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"
                        class="h-4 w-4 lg:h-6 lg:w-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" />
                    </svg>
                </span>
                <span class="text-sm lg:text-lg font-semibold">{{ $quote->user->name }}</span>
            </div>

            {{-- Right: like icon/count + date --}}
            <div class="flex items-center gap-4 pr-6">

             <span class=" text-xs lg:text-sm text-quote-text-secondary-light dark:text-quote-text-secondary-dark">
                    {{ $quote->created_at->format('M j, Y') }}
                </span>
                @auth
                    @if ($quote->isLikedBy(auth()->user()))
                        <form method="POST" action="{{ route('quotes.unlike', $quote) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" aria-label="Unlike this quote"
                                class="flex items-center gap-1 rounded-full px-2 py-1 text-sm font-medium text-quote-accent transition hover:bg-quote-accent/10">
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
                                class="flex items-center gap-1 rounded-full px-2 py-1 text-sm font-medium text-quote-text-secondary-light transition hover:bg-quote-accent/10 hover:text-quote-accent dark:text-quote-text-secondary-dark">
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
                        class="flex items-center gap-1 rounded-full px-2 py-1 text-sm font-medium text-quote-text-secondary-light transition hover:text-quote-accent dark:text-quote-text-secondary-dark">
                        <svg aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"
                            class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                        </svg>
                        {{ $quote->likers->count() }}
                    </a>
                @endauth

               
            </div>
        </div>
    </div>
</blockquote>
