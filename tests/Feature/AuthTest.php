<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

pest()->use(RefreshDatabase::class);

test('User Login', function () {
    $user = createUser();

    $response = postJson('/api/login', [
        'username' => $user->username,
        'password' => 'password'
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'message',
        'user' => [
            'id',
            'username',
        ],
        'token',
    ]);
});

test('User Logout', function () {
    $user = createUser();

    Sanctum::actingAs($user);

    $response = postJson('/api/logout');
    $response->assertStatus(200);
});

test('User Info', function () {
    $user = createUser();

    Sanctum::actingAs($user);

    $response = getJson('/api/me');
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'id',
        'username'
    ]);
});
