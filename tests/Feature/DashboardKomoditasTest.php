<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('Halaman komoditas pada dashboard bisa ditampilkan', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
        'is_admin' => true
    ]);

    $response = $this->actingAs($user)->get('/dashboard/komoditas');

    $response->assertStatus(200);
});

test('')