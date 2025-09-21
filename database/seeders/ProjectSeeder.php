<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some specific projects
        Project::create([
            'name' => 'Laravel Authentication System',
            'description' => 'Build a complete authentication system with signup, login, and user management features.',
        ]);

        Project::create([
            'name' => 'Task Management App',
            'description' => 'Create a project management application with tasks, deadlines, and team collaboration.',
        ]);

        Project::create([
            'name' => 'E-commerce Platform',
            'description' => 'Develop a full-featured online store with product catalog, shopping cart, and payment processing.',
        ]);

        // Create additional random projects
        Project::factory(7)->create();
    }
}
