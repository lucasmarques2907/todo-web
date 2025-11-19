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

test('Create List', function () {
    $user = createUser();

    Sanctum::actingAs($user);

    $payload = [
        "user_id" => $user->id,
        "title" => "new title",
        "description" => "new description"
    ];

    $response = postJson('/api/list', $payload);
    $response->assertStatus(201);
    $response->assertJsonStructure([
        'message',
        'list' => [
            'id',
            'title',
            'description'
        ]
    ]);
    assertDatabaseHas('lists', $payload);
});

test('Read List with id', function () {
    $user = createUser();

    Sanctum::actingAs($user);

    $list = createTodoList($user);

    $response = getJson("/api/list/$list->id");
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'message',
        'data' => [
            'id',
            'title',
            'description',
            'items' => []
        ]
    ]);
});

test('Read all Lists', function () {
    $user = createUser();

    Sanctum::actingAs($user);

    createTodoList($user);
    createTodoList($user);

    $response = getJson("/api/list");
    $response->assertStatus(200);
    $response->assertJsonStructure(
        [
            'data' => [
                ['id', 'title', 'description', 'items' => []],
                ['id', 'title', 'description', 'items' => []],
            ]
        ]
    );
});

test('Update List', function () {
    $user = createUser();

    Sanctum::actingAs($user);

    $list = createTodoList($user);

    $payload = [
        "title" => "updated title",
        "description" => "updated description",
    ];

    $response = patchJson("/api/list/$list->id", $payload);
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'message',
        'list' => [
            'id',
            'title',
            'description'
        ]
    ]);
    assertDatabaseHas('lists', $payload);
});

test('Delete List', function () {
    $user = createUser();

    Sanctum::actingAs($user);

    $list = createTodoList($user);

    $response = deleteJson("/api/list/$list->id");
    $response->assertStatus(200);
    $response->assertJsonStructure(['message']);
    assertDatabaseMissing('lists',  [
        "id" => $list->id
    ]);
});
