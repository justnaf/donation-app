<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register with default data (no avatar)', function () {
    $userData = [
        'name' => 'Test User',
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $response = $this->post('/register', $userData);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $this->assertDatabaseCount('users', 1);

    $user = User::first();

    expect($user->name)->toBe('Test User');
    expect($user->username)->toBe('testuser');

    expect($user->avatar)->toContain('https://ui-avatars.com/api/');
});


test('new users can register with an uploaded avatar', function () {
    Storage::fake('public');

    $userData = [
        'name' => 'Avatar User',
        'username' => 'avataruser',
        'email' => 'avatar@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'avatar' => UploadedFile::fake()->image('avatar.jpg', 100, 100)->size(50),
    ];

    $response = $this->post('/register', $userData);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $user = User::where('email', 'avatar@example.com')->first();

    expect($user->avatar)->not->toContain('https://ui-avatars.com/api/');
    Storage::disk('public')->assertExists($user->avatar);
});
