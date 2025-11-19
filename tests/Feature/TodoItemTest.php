<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;

pest()->use(RefreshDatabase::class);

test('Create Item', function () {
    $user = createUser();

    Sanctum::actingAs($user);

    $list = createTodoList($user);

    $payload = [
        "list_id" => $list->id,
        "title" => "new title",
        "description" => "new description"
    ];

    $response = postJson('/api/item', $payload);
    $response->assertStatus(201);
    $response->assertJsonStructure([
        'message',
        'item' => [
            'id',
            'title',
            'description',
            'completed'
        ]
    ]);
    assertDatabaseHas('items', $payload);
});

test('Update item', function () {
    $user = createUser();

    Sanctum::actingAs($user);

    $list = createTodoList($user);

    $item = createTodoItem($list);

    $payload = [
        "title" => "updated title",
        "description" => "updated description",
        "completed" => true,
    ];

    $response = patchJson("/api/item/$item->id", $payload);
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'message',
        'item' => [
            'id',
            'title',
            'description',
            'completed'
        ]
    ]);
    assertDatabaseHas('items', $payload);
});

test('Delete Item', function () {
    $user = createUser();

    Sanctum::actingAs($user);

    createTodoList();

    $item = createTodoItem();

    $response = deleteJson("/api/item/$item->id");
    $response->assertStatus(200);
    $response->assertJsonStructure(['message']);
    assertDatabaseMissing('items', [
        'id' => $item->id
    ]);
});
