<?php

namespace Database\Factories;

use App\Enums\StatusEnum;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->word(),
            'description' => fake()->text(255),
            'user_id' => User::factory()->create()->id,
            'status' => fake()->RandomElement(StatusEnum::cases())->value,
        ];
    }
}
