<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('El controlador de tareas enumera todas las tareas', function () {
    $tasks = Task::factory()->count(3)->create();

    $response = $this->getJson('/api/tasks');

    $response->assertOk()
        ->assertJsonCount(3)
        ->assertJsonFragment(['id' => $tasks->first()->id]);
});

test('El controlador de tareas almacena una tarea', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/tasks', [
        'name' => 'Preparar pruebas',
        'user_id' => $user->id,
    ]);

    $response->assertCreated()
        ->assertJsonFragment([
            'name' => 'Preparar pruebas',
            'user_id' => $user->id,
        ]);

    $this->assertDatabaseHas('tasks', [
        'name' => 'Preparar pruebas',
        'user_id' => $user->id,
    ]);
});

test('El controlador de tareas muestra una tarea', function () {
    $task = Task::factory()->create(['name' => 'Revisar controlador']);

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertOk()
        ->assertJsonFragment([
            'id' => $task->id,
            'name' => 'Revisar controlador',
        ]);
});

test('El controlador de tareas actualiza una tarea', function () {
    $task = Task::factory()->create();
    $user = User::factory()->create();

    $response = $this->putJson("/api/tasks/{$task->id}", [
        'name' => 'Actualizar tarea',
        'user_id' => $user->id,
    ]);

    $response->assertOk()
        ->assertJsonFragment([
            'id' => $task->id,
            'name' => 'Actualizar tarea',
            'user_id' => $user->id,
        ]);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'name' => 'Actualizar tarea',
        'user_id' => $user->id,
    ]);
});

test('El controlador de tareas elimina una tarea', function () {
    $task = Task::factory()->create();

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertNoContent();
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});
