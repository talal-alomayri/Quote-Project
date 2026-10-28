@extends('Layout.app')

@section('title', 'Profile')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8">

        {{-- Profile header --}}
        <div
            class="mb-10 rounded-2xl bg-quote-card-light p-6 shadow transition-colors duration-300 dark:bg-quote-card-dark">
            <div class="flex justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="flex h-16 w-16 items-center justify-center rounded-full bg-quote-accent text-2xl font-bold text-white">
                    {{ mb_substr($user->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-quote-text-primary-light dark:text-quote-text-primary-dark">
                        {{ $user->name }}
                    </h1>
                    <p class="text-quote-text-secondary-light dark:text-quote-text-secondary-dark">{{ $user->email }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="rounded-2xl bg-quote-accent px-5 py-2 font-medium text-white shadow-md transition-colors duration-300 hover:bg-quote-accent-hover focus:outline-none focus-visible:ring-4 focus-visible:ring-quote-accent/40">
                    Log Out
                </button>
            </form>
</div>

            <div>
                 <h2 class="mt-8 text-xl font-bold text-quote-text-primary-light dark:text-quote-text-primary-dark">
                Edit Profile
            </h2>

            <form method="POST" action="{{ route('profile.update') }}"
                class="space-y-5 rounded-2xl bg-quote-card-light p-6 shadow transition-colors duration-300 dark:bg-quote-card-dark">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="label">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required
                        minlength="2" maxlength="50" pattern="[A-Za-z\u0600-\u06FF ]+"
                        class="input @error('name') border-red-500 dark:border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="label">Email Address</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                        pattern="[^@\s]+@[^@\s]+\.[A-Za-z]{2,}"
                        class="input @error('email') border-red-500 dark:border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="label">New Password</label>
                    <input id="password" name="password" type="password" minlength="8" autocomplete="new-password"
                        class="input @error('password') border-red-500 dark:border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400" role="alert">{{ $message }}</p>
                    @else
                        <p class="mt-1 text-xs text-quote-text-secondary-light dark:text-quote-text-secondary-dark">
                            Leave blank to keep your current password. At least 8 characters.</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="label">Confirm New Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" minlength="8"
                        autocomplete="new-password" class="input">
                </div>

                <button type="submit"
                    class="w-full rounded-2xl bg-quote-accent px-6 py-3 font-medium text-white shadow-md transition-colors duration-300 hover:bg-quote-accent-hover focus:outline-none focus-visible:ring-4 focus-visible:ring-quote-accent/40 sm:w-auto">
                    Save Changes
                </button>
            </form>
            </div>
        </div>
    </div>
@endsection
