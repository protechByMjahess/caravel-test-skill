<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all projects
        $projects = Project::all();

        // Create tasks for each project
        foreach ($projects as $project) {
            // Create 3-8 tasks per project
            $taskCount = fake()->numberBetween(3, 8);
            
            for ($i = 0; $i < $taskCount; $i++) {
                Task::create([
                    'project_id' => $project->id,
                    'title' => fake()->sentence(4),
                    'status' => fake()->randomElement(['todo', 'in_progress', 'done']),
                    'due_date' => fake()->optional(0.7)->dateTimeBetween('now', '+30 days'),
                ]);
            }
        }

        // Create additional random tasks
        Task::factory(20)->create();
    }
}
