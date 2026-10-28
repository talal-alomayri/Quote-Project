<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the authenticated user's profile form.
     */
    public function index(Request $request): View
    {
        return view('profile.index', [
            'page' => 'Profile',
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the authenticated user's name, email and (optionally) password.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->safe()->only(['name', 'email']));

        // The `hashed` cast on the User model takes care of hashing the password.
        if ($request->filled('password')) {
            $user->password = $request->validated('password');
        }

        $user->save();

        return redirect()->route('profile.index')->with('success', 'Your profile has been updated.');
    }
}
