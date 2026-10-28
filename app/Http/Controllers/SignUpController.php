<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignUpRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class SignUpController extends Controller
{
    /**
     * Validate the registration input, persist the user and log them in.
     */
    public function store(SignUpRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
        ]);

        // Log the user in immediately after registration.
        Auth::login($user);

        // Regenerate the session to prevent session fixation.
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'Welcome to QuoteIt! Your account has been created.');
    }
}
