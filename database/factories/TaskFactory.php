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
            'project_id' => \App\Models\Project::factory(),
            'title' => fake()->sentence(4),
            'status' => fake()->randomElement(['todo', 'in_progress', 'done']),
            'due_date' => fake()->optional(0.7)->dateTimeBetween('now', '+30 days'),
        ];
    }
}
