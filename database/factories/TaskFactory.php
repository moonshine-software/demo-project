<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
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
            'status_id' => \App\Models\Status::query()->inRandomOrder()->value('id'),
            'title' => $this->faker->sentence(),
            'user_id' => \App\Models\User::query()->inRandomOrder()->value('id'),
            'description' => $this->faker->optional()->paragraph(),
            'tags' => $this->faker->optional()->randomElements(['urgent', 'bug', 'feature', 'enhancement', 'documentation'], $this->faker->numberBetween(0, 3)),
        ];
    }
}
