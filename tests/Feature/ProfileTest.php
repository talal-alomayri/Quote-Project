<?php

use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('redirects guests away from the profile page', function () {
    $this->get('/profile')->assertRedirect(route('login'));
    $this->put('/profile', ['name' => 'Guest User', 'email' => 'guest@example.com'])->assertRedirect(route('login'));
});

it('shows the created quotes, liked quotes and edit form sections', function () {
    $user = User::factory()->create();
    Quote::factory()->for($user)->create(['body' => 'A quote I wrote myself.']);
    $likedQuote = Quote::factory()->create(['body' => 'A quote I liked.']);

    $user->likedQuotes()->attach($likedQuote);

    $this->actingAs($user)
        ->get('/profile')
        ->assertOk()
        ->assertSee('My Created Quotes')
        ->assertSee('Liked Quotes')
        ->assertSee('Edit Profile')
        ->assertSee('A quote I wrote myself.')
        ->assertSee('A quote I liked.');
});

it('does not show quotes the user never wrote or liked', function () {
    $user = User::factory()->create();
    Quote::factory()->create(['body' => 'A completely unrelated quote.']);

    $this->actingAs($user)
        ->get('/profile')
        ->assertOk()
        ->assertDontSee('A completely unrelated quote.');
});

it('updates the name and email address', function () {
    $user = User::factory()->create(['name' => 'Old Name', 'email' => 'old@example.com']);

    $this->actingAs($user)
        ->put('/profile', ['name' => 'New Name', 'email' => 'new@example.com'])
        ->assertRedirect(route('profile.index'))
        ->assertSessionHas('success');

    expect($user->fresh())
        ->name->toBe('New Name')
        ->email->toBe('new@example.com');
});

it('allows the user to keep their current email address', function () {
    $user = User::factory()->create(['email' => 'same@example.com']);

    $this->actingAs($user)
        ->put('/profile', ['name' => 'Same Email', 'email' => 'same@example.com'])
        ->assertRedirect(route('profile.index'))
        ->assertSessionHasNoErrors();
});

it('hashes a new password when one is provided', function () {
    $user = User::factory()->create(['name' => 'Hashed Password', 'password' => Hash::make('old-password')]);

    $this->actingAs($user)->put('/profile', [
        'name' => $user->name,
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertRedirect(route('profile.index'));

    $fresh = $user->fresh();

    expect($fresh->password)->not->toBe('new-password');
    expect(Hash::check('new-password', $fresh->password))->toBeTrue();
});

it('keeps the current password when the password field is left blank', function () {
    $user = User::factory()->create(['password' => Hash::make('keep-me-please')]);

    $this->actingAs($user)->put('/profile', [
        'name' => 'Renamed User',
        'email' => $user->email,
        'password' => '',
    ])->assertRedirect(route('profile.index'));

    expect(Hash::check('keep-me-please', $user->fresh()->password))->toBeTrue();
});

it('rejects invalid profile input', function (array $payload, string $error) {
    $user = User::factory()->create(['name' => 'Original Name', 'email' => 'original@example.com']);
    User::factory()->create(['email' => 'taken@example.com']);

    $this->actingAs($user)->put('/profile', $payload)->assertSessionHasErrors($error);

    expect($user->fresh()->name)->toBe('Original Name');
})->with([
    'missing name' => [['name' => '', 'email' => 'original@example.com'], 'name'],
    'name with digits' => [['name' => 'Bad1', 'email' => 'original@example.com'], 'name'],
    'invalid email' => [['name' => 'Fine Name', 'email' => 'not-an-email'], 'email'],
    'email taken by another user' => [['name' => 'Fine Name', 'email' => 'taken@example.com'], 'email'],
    'short password' => [
        ['name' => 'Fine Name', 'email' => 'original@example.com', 'password' => 'short', 'password_confirmation' => 'short'],
        'password',
    ],
    'password confirmation mismatch' => [
        ['name' => 'Fine Name', 'email' => 'original@example.com', 'password' => 'new-password', 'password_confirmation' => 'other-password'],
        'password',
    ],
]);

it('stores an uploaded avatar on the public disk', function () {
    Storage::fake('public');

    $user = User::factory()->create(['name' => 'Avatar Owner', 'email' => 'avatar@example.com']);

    $this->actingAs($user)->put('/profile', [
        'name' => $user->name,
        'email' => $user->email,
        'avatar' => UploadedFile::fake()->image('me.jpg', 200, 200),
    ])->assertRedirect(route('profile.index'))->assertSessionHasNoErrors();

    $avatar = $user->fresh()->avatar;

    expect($avatar)->toStartWith('avatars/');

    Storage::disk('public')->assertExists($avatar);
});

it('replaces the old avatar file when a new one is uploaded', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'name' => 'Avatar Owner',
        'email' => 'avatar@example.com',
        'avatar' => UploadedFile::fake()->image('old.jpg')->store('avatars', 'public'),
    ]);

    $oldAvatar = $user->avatar;

    $this->actingAs($user)->put('/profile', [
        'name' => $user->name,
        'email' => $user->email,
        'avatar' => UploadedFile::fake()->image('new.jpg', 200, 200),
    ])->assertSessionHasNoErrors();

    $newAvatar = $user->fresh()->avatar;

    expect($newAvatar)->not->toBe($oldAvatar);

    Storage::disk('public')->assertMissing($oldAvatar);
    Storage::disk('public')->assertExists($newAvatar);
});

it('keeps the current avatar when no new file is uploaded', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'name' => 'Avatar Owner',
        'email' => 'avatar@example.com',
        'avatar' => UploadedFile::fake()->image('keep.jpg')->store('avatars', 'public'),
    ]);

    $avatar = $user->avatar;

    $this->actingAs($user)->put('/profile', [
        'name' => 'Renamed Owner',
        'email' => $user->email,
    ])->assertSessionHasNoErrors();

    expect($user->fresh()->avatar)->toBe($avatar);

    Storage::disk('public')->assertExists($avatar);
});

it('rejects an invalid avatar upload', function (array $payload, string $error) {
    Storage::fake('public');

    $user = User::factory()->create(['name' => 'Avatar Owner', 'email' => 'avatar@example.com']);

    $this->actingAs($user)->put('/profile', $payload)->assertSessionHasErrors($error);

    expect($user->fresh()->avatar)->toBeNull();
})->with([
    'not an image' => [
        ['name' => 'Avatar Owner', 'email' => 'avatar@example.com', 'avatar' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf')],
        'avatar',
    ],
    'unsupported image type' => [
        ['name' => 'Avatar Owner', 'email' => 'avatar@example.com', 'avatar' => UploadedFile::fake()->image('anim.gif')],
        'avatar',
    ],
    'image too large' => [
        ['name' => 'Avatar Owner', 'email' => 'avatar@example.com', 'avatar' => UploadedFile::fake()->image('huge.jpg')->size(3000)],
        'avatar',
    ],
]);

it('renders the uploaded avatar in the navbar and the fallback initial otherwise', function () {
    Storage::fake('public');

    $withPhoto = User::factory()->create([
        'name' => 'Photo Person',
        'email' => 'photo@example.com',
        'avatar' => UploadedFile::fake()->image('photo.jpg')->store('avatars', 'public'),
    ]);

    $this->actingAs($withPhoto)->get('/')->assertSee(asset('storage/'.$withPhoto->avatar), false);

    $withoutPhoto = User::factory()->create([
        'name' => 'Zainab Noor',
        'email' => 'plain@example.com',
        'avatar' => null,
    ]);

    $this->actingAs($withoutPhoto)->get('/')->assertSee('Z', false);
});
