<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Hash; 


test('Halaman Login bisa ditampilkan', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('Pengguna dapat mengautentikasi dengan validasi dan reCAPTCHA benar', function () {
    // Mocking the response from the reCAPTCHA API
    Http::fake([
        'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true]),
    ]);

    // Create a user with a hashed password
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password', // Provide the actual password, Laravel will hash it automatically for comparison
        'g-recaptcha-response' => 'valid-recaptcha-response', // Add a valid reCAPTCHA response
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(RouteServiceProvider::HOME);
});

test('Pengguna tidak dapat login dengan respon reCAPTCHA yang tidak benar & kata sandi salah', function () {
    // Mocking the response from the reCAPTCHA API
    Http::fake([
        'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => false]),
    ]);

    // Create a user with a hashed password
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password', 
        'g-recaptcha-response' => 'invalid-recaptcha-response',
    ]);

    $this->assertGuest();
    
});
