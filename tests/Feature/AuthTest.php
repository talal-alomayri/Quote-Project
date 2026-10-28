<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('renders the sign up and log in pages', function () {
    $this->get('/signup')->assertOk()->assertSee('name="email"', false);
    $this->get('/login')->assertOk()->assertSee('name="email"', false);
});

it('registers a new user, persists it and logs them in', function () {
    $response = $this->post('/signup', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('home'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
    ]);

    $this->assertAuthenticated();
    expect(auth()->user()->email)->toBe('jane@example.com');
});

it('hashes the password when registering', function () {
    $this->post('/signup', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password123',
    ]);

    $user = User::where('email', 'jane@example.com')->firstOrFail();

    expect($user->password)->not->toBe('password123');
    expect(Hash::check('password123', $user->password))->toBeTrue();
});

it('rejects invalid sign up input', function (array $payload, string $field) {
    $this->post('/signup', $payload)->assertSessionHasErrors($field);
    $this->assertGuest();
})->with([
    'name too short' => [['name' => 'A', 'email' => 'a@example.com', 'password' => 'password123'], 'name'],
    'name with digits' => [['name' => 'Jane1', 'email' => 'a@example.com', 'password' => 'password123'], 'name'],
    'invalid email' => [['name' => 'Jane Doe', 'email' => 'not-an-email', 'password' => 'password123'], 'email'],
    'short password' => [['name' => 'Jane Doe', 'email' => 'a@example.com', 'password' => 'short'], 'password'],
]);

it('rejects a duplicate email', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->post('/signup', [
        'name' => 'Jane Doe',
        'email' => 'taken@example.com',
        'password' => 'password123',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('logs a user in with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'jane@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->post('/login', [
        'email' => 'jane@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('home'));
    $response->assertSessionHas('success');

    $this->assertAuthenticatedAs($user);
});

it('fails to log in with an incorrect password and keeps the email', function () {
    $user = User::factory()->create([
        'email' => 'jane@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->from('/login')->post('/login', [
        'email' => 'jane@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors('email');
    $response->assertSessionHasInput('email', 'jane@example.com');

    $this->assertGuest();
});
