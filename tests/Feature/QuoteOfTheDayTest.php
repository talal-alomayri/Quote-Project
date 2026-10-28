<?php

use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function () {
    // The selected quote id is cached globally, so isolate it between tests.
    Cache::flush();
});

it('shows the quote of the day above the regular feed', function () {
    Quote::factory()->create(['body' => 'Only one quote available today.']);

    $this->get('/')
        ->assertOk()
        ->assertSee('Quote of the Day')
        ->assertSeeInOrder(['Quote of the Day', 'Only one quote available today.']);
});

it('keeps the same quote selected for the whole day', function () {
    $bodies = ['Alpha quote.', 'Bravo quote.', 'Charlie quote.', 'Delta quote.', 'Echo quote.'];

    foreach ($bodies as $body) {
        Quote::factory()->create(['body' => $body]);
    }

    $this->get('/')->assertOk();

    $selectedId = Cache::get('quote_of_the_day');
    expect($selectedId)->not->toBeNull();

    $selected = Quote::find($selectedId);

    // Every later visit must re-use the cached pick rather than re-rolling.
    foreach (range(1, 5) as $visit) {
        $this->get('/')
            ->assertOk()
            ->assertSeeInOrder(['Quote of the Day', $selected->body]);
    }

    expect(Cache::get('quote_of_the_day'))->toBe($selectedId);
});

it('passes the same quote instance to the view on every request', function () {
    Quote::factory()->count(3)->create();

    $first = $this->get('/')->viewData('quoteOfTheDay');
    $second = $this->get('/')->viewData('quoteOfTheDay');

    expect($second->id)->toBe($first->id);
});

it('loads cleanly and hides the section when there are no quotes', function () {
    $this->get('/')
        ->assertOk()
        ->assertDontSee('Quote of the Day')
        ->assertSee('No quotes yet');
});

it('throws no error when the selected quote is deleted during the day', function () {
    $quote = Quote::factory()->create();
    $this->get('/')->assertOk();

    $quote->delete();

    $this->get('/')->assertOk()->assertDontSee('Quote of the Day');
});

it('lets a logged in user like the quote of the day', function () {
    $user = User::factory()->create();
    $quote = Quote::factory()->create(['body' => 'A very likeable quote.']);
    $other = Quote::factory()->create();

    // Pick a known quote, then make sure the card's like button targets it.
    Cache::forever('quote_of_the_day', $quote->id);

    $this->actingAs($user)
        ->get('/')
        ->assertOk()
        ->assertSee(route('quotes.like', $quote), false);

    $this->actingAs($user)
        ->post("/quotes/{$quote->id}/like")
        ->assertRedirect(route('home'));

    expect($quote->likers()->count())->toBe(1);
    expect($other->likers()->count())->toBe(0);
});

it('redirects guests to the login page when they like the quote of the day', function () {
    $quote = Quote::factory()->create();
    Cache::forever('quote_of_the_day', $quote->id);

    $this->get('/')
        ->assertOk()
        ->assertSee(route('login'), false)
        ->assertDontSee(route('quotes.like', $quote), false);

    $this->post("/quotes/{$quote->id}/like")->assertRedirect(route('login'));

    expect($quote->likers()->count())->toBe(0);
});
