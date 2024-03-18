<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Hash; 


test('Halaman Login bisa di render', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('User dapat mengautentikasi dengan validasi dan reCAPTCHA benar', function () {
    // Mocking the response from the reCAPTCHA API
    Http::fake([
        'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true]),
    ]);

    // Create a user with a hashed password
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password', // Provide the actual password, Laravel will hash it automatically for comparison
        'g-recaptcha-response' => 'valid-recaptcha-response', // Add a valid reCAPTCHA response
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(RouteServiceProvider::HOME);
});

test('User tidak dapat login dengan respons reCAPTCHA yang tidak valid & password salah', function () {
    // Mocking the response from the reCAPTCHA API
    Http::fake([
        'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => false]),
    ]);

    // Create a user with a hashed password
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password', 
        'g-recaptcha-response' => 'invalid-recaptcha-response',
    ]);

    $this->assertGuest();
    
});
