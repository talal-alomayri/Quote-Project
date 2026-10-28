@extends('Layout.AuthImg')

@section('title', 'Sign Up')

@section('form')
    <div class="flex items-center justify-center p-8">
        {{-- `novalidate` lets the server side render clear, styled messages instead of the browser's default tooltip --}}
        <form action="{{ route('signup.store') }}" method="POST" novalidate
            class="mx-auto mt-5 w-full max-w-xl rounded-2xl bg-quote-card-light p-9 shadow transition-colors duration-300 dark:bg-quote-card-dark">
            @csrf

            <h2 class="mb-4 text-center text-2xl font-bold text-quote-text-primary-light dark:text-quote-text-primary-dark">
                Sign Up</h2>

            <div class="mb-4">
                <label for="name" class="label">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}"
                    class="input peer @error('name') border-red-500 dark:border-red-500 @enderror" required minlength="2"
                    maxlength="50" pattern="[A-Za-z\u0600-\u06FF ]+">
                @error('name')
                    <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400" role="alert">{{ $message }}</p>
                @else
                    <p class="mt-1 text-xs text-quote-text-secondary-light dark:text-quote-text-secondary-dark">
                        2–50 characters, letters only.</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="label">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}"
                    class="input peer @error('email') border-red-500 dark:border-red-500 @enderror" required
                    pattern="[^@\s]+@[^@\s]+\.[A-Za-z]{2,}">
                @error('email')
                    <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400" role="alert">{{ $message }}</p>
                @else
                    <p class="mt-1 text-xs text-quote-text-secondary-light dark:text-quote-text-secondary-dark">
                        A valid email, e.g. name@example.com</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="label">Password</label>
                <input id="password" name="password" type="password"
                    class="input peer @error('password') border-red-500 dark:border-red-500 @enderror" required minlength="8">
                @error('password')
                    <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400" role="alert">{{ $message }}</p>
                @else
                    <p class="mt-1 text-xs text-quote-text-secondary-light dark:text-quote-text-secondary-dark">
                        At least 8 characters.</p>
                @enderror
            </div>

            <p class="mb-4 text-sm text-quote-text-secondary-light dark:text-quote-text-secondary-dark">
                You already have an account?
                <a href="{{ route('login') }}" class="font-medium text-quote-accent hover:underline">Log In</a>
            </p>

            <button type="submit"
                class="w-full rounded-2xl bg-quote-accent px-6 py-3 text-lg font-medium text-white shadow-md transition-colors duration-300 hover:bg-quote-accent-hover focus:outline-none focus-visible:ring-4 focus-visible:ring-quote-accent/40">
                Sign Up
            </button>
        </form>
    </div>
@endsection
