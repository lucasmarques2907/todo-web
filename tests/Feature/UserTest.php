<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;

pest()->use(RefreshDatabase::class);

test('Create User', function () {
    $payload = [
        "username" => "user123",
        "password" => "password",
        "password_confirmation" => "password",
    ];

    $response = postJson('/api/user', $payload);
    $response->assertStatus(201);
    $response->assertJsonStructure([
        'message',
        'user' => [
            'id',
            'username',
        ]
    ]);
    assertDatabaseHas('users', ['username' => $payload['username']]);
});

test('Read User with id', function () {
    $user = createUser();

    Sanctum::actingAs($user);

    $response = getJson("/api/user/$user->id");
    $response->assertStatus(200);
    $response->assertJsonStructure(['user' => ['id', 'username']]);
});

test('Read all Users', function () {
    createUser();
    createUser();

    $response = getJson("/api/user");
    $response->assertStatus(200);
    $response->assertJsonStructure(
        [
            'data' => [
                ['id', 'username'],
                ['id', 'username'],
            ]
        ]
    );
});

test('Update User', function () {
    $user = createUser();

    Sanctum::actingAs($user);

    $payload = [
        "username" => fake()->regexify('[A-Za-z0-9]{8}'),
        "password" => "password",
        "password_confirmation" => "password"
    ];

    $response = patchJson("/api/user/$user->id", $payload);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'message',
        'user' => [
            'id',
            'username'
        ]
    ]);
});

test('Delete User', function () {
    $user = createUser();

    Sanctum::actingAs($user);

    $response = deleteJson("/api/user/$user->id");

    $response->assertStatus(200);
    $response->assertJsonStructure(['message']);
    assertDatabaseMissing('users', [
        "id" => $user->id
    ]);
});
