<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'My Website')</title>

    {{-- Google Fonts — Plus Jakarta Sans for the UI, Playfair Display for quote text --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400..900&family=Plus+Jakarta+Sans:wght@200..800&display=swap">

    {{-- Apply the saved/system theme before paint to prevent a flash of unstyled content (FOUC) --}}
    <script>
        (function () {
            const stored = localStorage.getItem('color-theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (stored === 'dark' || (stored === null && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="min-h-screen bg-quote-bg-light dark:bg-quote-bg-dark text-quote-text-primary-light dark:text-quote-text-primary-dark font-sans antialiased transition-colors duration-300"
>
    <x-navbar :page="$page ?? ''" />

    @if (session('success'))
        <div id="flash-success-message"
             class="mx-auto mt-4 max-w-7xl px-4 transition-opacity duration-500 ease-out">
            <div class="rounded-2xl border border-quote-border-light bg-quote-card-light px-4 py-3 text-sm font-medium text-green-600 transition-colors duration-300 dark:border-quote-border-dark dark:bg-quote-card-dark dark:text-green-400">
                {{ session('success') }}
            </div>
        </div>

        <script>
            setTimeout(() => {
                const flashMessage = document.getElementById('flash-success-message');
                if (flashMessage) {
                    flashMessage.style.opacity = '0'; // بتخلي الرسالة تتبخر (Fade out)
                    setTimeout(() => flashMessage.remove(), 500); // بتحذفها تماماً من الـ DOM بعد انتهاء التأثير
                }
            }, 3000); // 3000 ميللي ثانية = 3 ثواني
        </script>
    @endif

    <main>
        @yield('content')
    </main>

</body>

</html>
