<?php

use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows quotes to guests without any editing affordances', function () {
    Quote::factory()->create(['body' => 'Simplicity is the soul of efficiency.']);

    $this->get('/')
        ->assertOk()
        ->assertSee('Simplicity is the soul of efficiency.')
        ->assertSee(route('login'), false)
        ->assertDontSee('aria-label="Delete this quote"', false);

    $this->assertGuest();
});

it('renders the home page when no quotes exist', function () {
    $this->get('/')->assertOk()->assertSee('No quotes yet');
});

it('allows an authenticated user to share a quote', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/quotes', [
        'body' => 'The best way to predict the future is to invent it.',
    ]);

    $response->assertRedirect(route('home'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('quotes', [
        'user_id' => $user->id,
        'body' => 'The best way to predict the future is to invent it.',
    ]);
});

it('rejects quote input that fails validation', function (array $payload, string $error) {
    $this->actingAs(User::factory()->create())
        ->post('/quotes', $payload)
        ->assertSessionHasErrors($error);

    expect(Quote::count())->toBe(0);
})->with([
    'missing body' => [[], 'body'],
    'body too short' => [['body' => 'Hi'], 'body'],
    'body too long' => [['body' => str_repeat('a', 156)], 'body'],
]);

it('forbids guests from sharing a quote', function () {
    $this->post('/quotes', ['body' => 'A guest should not be able to post this.'])
        ->assertRedirect(route('login'));

    expect(Quote::count())->toBe(0);
});

it('lets the author update their own quote', function () {
    $user = User::factory()->create();
    $quote = Quote::factory()->for($user)->create(['body' => 'Original wording.']);

    $this->actingAs($user)
        ->put("/quotes/{$quote->id}", ['body' => 'Improved wording.'])
        ->assertRedirect(route('home'));

    expect($quote->fresh()->body)->toBe('Improved wording.');
});

it('forbids updating a quote owned by another user', function () {
    $quote = Quote::factory()->create(['body' => 'Not yours.']);

    $this->actingAs(User::factory()->create())
        ->put("/quotes/{$quote->id}", ['body' => 'Hijacked.'])
        ->assertForbidden();

    expect($quote->fresh()->body)->toBe('Not yours.');
});

it('lets the author delete their own quote', function () {
    $user = User::factory()->create();
    $quote = Quote::factory()->for($user)->create();

    $this->actingAs($user)
        ->delete("/quotes/{$quote->id}")
        ->assertRedirect(route('home'));

    $this->assertDatabaseMissing('quotes', ['id' => $quote->id]);
});

it('forbids deleting a quote owned by another user', function () {
    $quote = Quote::factory()->create();

    $this->actingAs(User::factory()->create())
        ->delete("/quotes/{$quote->id}")
        ->assertForbidden();

    $this->assertDatabaseHas('quotes', ['id' => $quote->id]);
});

it('forbids guests from liking a quote', function () {
    $quote = Quote::factory()->create();

    $this->post("/quotes/{$quote->id}/like")->assertRedirect(route('login'));

    expect($quote->likers()->count())->toBe(0);
});

it('lets an authenticated user like and unlike a quote', function () {
    $user = User::factory()->create();
    $quote = Quote::factory()->create();

    $this->actingAs($user)->post("/quotes/{$quote->id}/like")->assertRedirect(route('home'));

    expect($quote->likers()->count())->toBe(1);
    expect($user->likedQuotes()->count())->toBe(1);

    $this->actingAs($user)->delete("/quotes/{$quote->id}/like")->assertRedirect(route('home'));

    expect($quote->likers()->count())->toBe(0);
});

it('does not record the same like twice', function () {
    $user = User::factory()->create();
    $quote = Quote::factory()->create();

    $this->actingAs($user)->post("/quotes/{$quote->id}/like");
    $this->actingAs($user)->post("/quotes/{$quote->id}/like");

    expect($quote->likers()->count())->toBe(1);
});

it('logs an authenticated user out', function () {
    $this->actingAs(User::factory()->create())
        ->post('/logout')
        ->assertRedirect(route('home'));

    $this->assertGuest();
});
