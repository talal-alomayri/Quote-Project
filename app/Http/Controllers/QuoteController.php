<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteRequest;
use App\Models\Quote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class QuoteController extends Controller
{
    /**
     * Show the quote of the day plus the feed of quotes.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $myQuotes = $user
            ? $user->quotes()->with(['user', 'likers'])->latest()->get()
            : collect();

        $otherQuotes = Quote::query()
            ->with('user')
            ->with('likers')
            ->when($user, fn ($query) => $query->where('user_id', '!=', $user->id))
            ->latest()
            ->get();

        return view('welcome', [
            'page' => 'Home',
            'myQuotes' => $myQuotes,
            'otherQuotes' => $otherQuotes,
            'quoteOfTheDay' => $this->quoteOfTheDay(),
        ]);
    }

    /**
     * Pick one quote at random and keep it until the end of the calendar day,
     * so every visitor sees the same quote of the day.
     */
    protected function quoteOfTheDay(): ?Quote
    {
        $quoteId = Cache::remember(
            'quote_of_the_day',
            now()->endOfDay(),
            fn () => Quote::inRandomOrder()->value('id'),
        );

        // Empty table, or the cached quote was deleted since it was picked.
        if ($quoteId === null) {
            return null;
        }

        return Quote::with(['user', 'likers'])->find($quoteId);
    }

    /**
     * Store a newly created quote for the authenticated user.
     */
    public function store(QuoteRequest $request): RedirectResponse
    {
        $request->user()->quotes()->create($request->validated());

        return redirect()->route('home')->with('success', 'Your quote has been shared.');
    }

    /**
     * Update one of the authenticated user's own quotes.
     */
    public function update(QuoteRequest $request, Quote $quote): RedirectResponse
    {
        $this->ensureQuoteBelongsToUser($request, $quote);

        $quote->update($request->validated());

        return redirect()->route('home')->with('success', 'Your quote has been updated.');
    }

    /**
     * Delete one of the authenticated user's own quotes.
     */
    public function destroy(Request $request, Quote $quote): RedirectResponse
    {
        $this->ensureQuoteBelongsToUser($request, $quote);

        $quote->delete();

        return redirect()->route('home')->with('success', 'Your quote has been deleted.');
    }

    /**
     * Show all quotes created by the authenticated user.
     */
    public function myQuotes(Request $request): View
    {
        $user = $request->user();

        $quotes = $user->quotes()
            ->with(['user', 'likers'])
            ->latest()
            ->paginate(15);

        return view('quotes.my-quotes', [
            'page'   => 'My Quotes',
            'quotes' => $quotes,
        ]);
    }

    /**
     * Show all quotes liked by the authenticated user.
     */
    public function likedQuotes(Request $request): View
    {
        $user = $request->user();

        $quotes = $user->likedQuotes()
            ->with(['user', 'likers'])
            ->latest('quote_user.created_at')
            ->paginate(15);

        return view('quotes.liked-quotes', [
            'page'   => 'Liked Quotes',
            'quotes' => $quotes,
        ]);
    }

    /**
     * Like a quote. Guests are rejected by the route middleware.
     */
    public function like(Request $request, Quote $quote): RedirectResponse
    {
        // `syncWithoutDetaching` keeps the unique pivot index happy on repeat likes.
        $quote->likers()->syncWithoutDetaching([$request->user()->id]);

        return redirect()->back();
    }

    /**
     * Remove the authenticated user's like from a quote.
     */
    public function unlike(Request $request, Quote $quote): RedirectResponse
    {
        $quote->likers()->detach($request->user()->id);

        return redirect()->back();
    }

    /**
     * Abort with a 403 when the quote belongs to somebody else.
     */
    protected function ensureQuoteBelongsToUser(Request $request, Quote $quote): void
    {
        abort_unless($quote->user_id === $request->user()->id, 403);
    }
}
