<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogInRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LogInController extends Controller
{
    /**
     * Attempt to authenticate the user and regenerate the session.
     */
    public function store(LogInRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt($credentials)) {
            // Send the user back with a clear inline error, keeping the email value.
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        // Regenerate the session to prevent session fixation.
        $request->session()->regenerate();

        return redirect()->intended(route('home'))->with('success', 'Welcome back!');
    }
}
