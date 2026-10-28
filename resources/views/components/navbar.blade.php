@props(['page' => ''])

<nav
    class="sticky top-0 z-50 w-full border-b border-quote-border-light bg-quote-card-light transition-colors duration-300 dark:border-quote-border-dark dark:bg-quote-card-dark">
    <div class="mx-auto flex items-center justify-between gap-4 px-4 py-2">

        {{-- Far left: avatar (authenticated only) + brand title --}}
        <div class="flex items-center gap-3">
            @auth
                <a href="/profile" aria-label="Profile"
                    class="rounded-full focus:outline-none focus-visible:ring-4 focus-visible:ring-quote-accent/40">
                    <span
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-quote-accent text-lg font-bold text-white ring-2 ring-quote-accent/50 transition duration-300 hover:scale-105">
                        {{ mb_substr(Auth::user()->name, 0, 1) }}
                    </span>
                </a>
            @endauth

            <a href="/"
                class="text-xl font-bold text-quote-text-primary-light transition-colors duration-300 hover:text-quote-accent dark:text-quote-text-primary-dark">
                QuoteIt
            </a>
        </div>

        {{-- Far right: theme toggle, then the auth action buttons --}}
        <div class="flex items-center gap-3">

            {{-- Theme toggle --}}
            <button id="theme-toggle" type="button" aria-label="Toggle dark mode"
                class="rounded-full p-2 text-quote-text-secondary-light transition duration-300 hover:bg-quote-accent/10 hover:text-quote-accent focus:outline-none focus-visible:ring-4 focus-visible:ring-quote-accent/30 dark:text-quote-text-secondary-dark dark:hover:bg-quote-accent/10">
                {{-- Sun icon: shown in dark mode (click to switch to light) --}}
                <svg class="hidden h-6 w-6 dark:block" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                </svg>
                {{-- Moon icon: shown in light mode (click to switch to dark) --}}
                <svg class="block h-6 w-6 dark:hidden" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                </svg>
            </button>

            @auth
                {{-- Authenticated: My Quotes + Liked Quotes --}}
                <a href="{{ route('quotes.my') }}"
                    class="  font-medium text-quote-text-primary-light transition-colors duration-300 hover:text-quote-accent dark:border-quote-border-dark dark:text-quote-text-primary-dark dark:hover:border-quote-accent dark:hover:text-quote-accent {{ $page === 'My Quotes' ? 'border-quote-accent text-quote-accent dark:border-quote-accent dark:text-quote-accent' : '' }}">
                    My Quotes
                </a>
                <a href="{{ route('quotes.liked') }}"
                    class=" font-medium text-quote-text-primary-light transition-colors duration-300 hover:text-quote-accent dark:border-quote-border-dark dark:text-quote-text-primary-dark dark:hover:border-quote-accent dark:hover:text-quote-accent {{ $page === 'Liked Quotes' ? 'border-quote-accent text-quote-accent dark:border-quote-accent dark:text-quote-accent' : '' }}">
                    Liked Quotes
                </a>
            @else
                {{-- Guest: Log In + Sign Up --}}
                <a href="/login"
                    class="rounded-2xl border border-quote-border-light px-5 py-2.5 font-medium text-quote-text-primary-light transition-colors duration-300 hover:border-quote-accent hover:text-quote-accent dark:border-quote-border-dark dark:text-quote-text-primary-dark dark:hover:border-quote-accent dark:hover:text-quote-accent">
                    Log In
                </a>
                <a href="/signup"
                    class="rounded-2xl bg-quote-accent px-5 py-2.5 font-medium text-white shadow-md transition-colors duration-300 hover:bg-quote-accent-hover focus:outline-none focus-visible:ring-4 focus-visible:ring-quote-accent/40">
                    Sign Up
                </a>
            @endauth
        </div>
    </div>
</nav>
