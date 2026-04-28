<?php

namespace Tests\Feature;

use App\Enums\StatusEnum;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDataBase;

    #[Test]
    public function guest_can_register(): void
    {
        $request = $this->postJson('api/v1/registration', [
            'name' => 'Test',
            'email' => 'example@mail.com',
            'password' => 'password',
        ]);

        $request->assertStatus(201);

        $this->assertDataBaseHas('users', [
            'name' => 'Test',
            'email' => 'example@mail.com',
        ]);
    }

    #[Test]
    public function guest_can_make_token(): void
    {
        User::create([
            'name' => 'Test',
            'email' => 'example@mail.com',
            'password' => 'password',
        ]);
        $request = $this->postJson('api/v1/authorization', [
            'email' => 'example@mail.com',
            'password' => 'password',
        ]);

        $request->assertStatus(200);
    }

    #[Test]
    public function guest_cannot_see_any_task(): void
    {
        $request = $this->postJson('api/v1/tasks', [
            'title' => 'Test',
            'description' => 'Test',
            'status' => StatusEnum::PENDING->value,
        ]);

        $request->assertStatus(401);
    }

    #[Test]
    public function guest_cannot_create_task(): void
    {
        $request = $this->postJson("api/v1/tasks", [
            'title' => 'Test',
            'description' => 'Test',
            'status' => StatusEnum::DONE->value,
        ]);

        $request->assertStatus(401);
    }

    #[Test]
    public function guest_cannot_change_any_task(): void
    {
        $task = Task::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'Test',
            'description' => 'Test',
            'status' => StatusEnum::PENDING->value,
        ]);
        $request = $this->putJson("api/v1/tasks/{$task->id}", [
            'title' => 'Test2',
            'description' => 'Test2',
            'status' => StatusEnum::DONE->value,
        ]);

        $request->assertStatus(401);
    }

    #[Test]
    public function guest_cannot_delete_any_task(): void
    {
        $task = Task::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'Test',
            'description' => 'Test',
            'status' => StatusEnum::PENDING->value,
        ]);
        $request = $this->deleteJson("api/v1/tasks/{$task->id}");

        $request->assertStatus(401);
    }
}
