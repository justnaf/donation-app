<?php

use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

// Tes diubah untuk lebih spesifik dan menggunakan field 'login'
test('users can authenticate using email', function () {
    $user = User::factory()->create();

    // Menggunakan kunci 'login' untuk mengirim email
    $response = $this->post('/login', [
        'login' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

// Tes BARU untuk memverifikasi login dengan username
test('users can authenticate using username', function () {
    $user = User::factory()->create();

    // Menggunakan kunci 'login' untuk mengirim username
    $response = $this->post('/login', [
        'login' => $user->username,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});


test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    // Menggunakan kunci 'login'
    $this->post('/login', [
        'login' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
