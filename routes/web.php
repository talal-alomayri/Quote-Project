<?php

use App\Http\Controllers\LogInController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SignUpController;
use Illuminate\Support\Facades\Route;

Route::get('/', [QuoteController::class, 'index'])->name('home');

Route::get('/signup', function () {
    return view('Authentication.SignUp', ['page' => 'Sign Up']);
})->name('signup');

Route::post('/signup', [SignUpController::class, 'store'])->name('signup.store');

Route::get('/login', function () {
    return view('Authentication.LogIn', ['page' => 'Log In']);
})->name('login');

Route::post('/login', [LogInController::class, 'store'])->name('login.store');

// Quote CRUD, likes and the profile page are reserved for authenticated users.
Route::middleware('auth')->group(function () {
    Route::post('/quotes', [QuoteController::class, 'store'])->name('quotes.store');
    Route::put('/quotes/{quote}', [QuoteController::class, 'update'])->name('quotes.update');
    Route::delete('/quotes/{quote}', [QuoteController::class, 'destroy'])->name('quotes.destroy');

    // Liking is strictly limited to logged-in users.
    Route::post('/quotes/{quote}/like', [QuoteController::class, 'like'])->name('quotes.like');
    Route::delete('/quotes/{quote}/like', [QuoteController::class, 'unlike'])->name('quotes.unlike');

    Route::get('/my-quotes', [QuoteController::class, 'myQuotes'])->name('quotes.my');
    Route::get('/liked-quotes', [QuoteController::class, 'likedQuotes'])->name('quotes.liked');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/logout', LogoutController::class)->name('logout');
});
