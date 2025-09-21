<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function user_can_view_projects_index()
    {
        $response = $this->get('/projects');
        
        $response->assertStatus(200);
        $response->assertViewIs('projects.index');
    }

    /** @test */
    public function user_can_create_project()
    {
        $projectData = [
            'name' => 'Test Project',
            'description' => 'This is a test project description'
        ];

        $response = $this->post('/projects', $projectData);

        $response->assertStatus(302); // Redirect after creation
        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'description' => 'This is a test project description',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function user_can_create_project_via_api()
    {
        $projectData = [
            'name' => 'API Test Project',
            'description' => 'This is an API test project'
        ];

        $response = $this->postJson('/projects', $projectData);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Project created successfully!'
        ]);
        
        $this->assertDatabaseHas('projects', [
            'name' => 'API Test Project',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function project_creation_requires_name()
    {
        $response = $this->post('/projects', [
            'description' => 'Project without name'
        ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function user_can_view_their_project()
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $response = $this->get("/projects/{$project->id}");

        $response->assertStatus(200);
        $response->assertViewIs('projects.show');
        $response->assertViewHas('project', $project);
    }

    /** @test */
    public function user_cannot_view_other_users_project()
    {
        $otherUser = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->get("/projects/{$project->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_update_their_project()
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'name' => 'Updated Project Name',
            'description' => 'Updated description'
        ];

        $response = $this->put("/projects/{$project->id}", $updateData);

        $response->assertStatus(302);
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project Name',
            'description' => 'Updated description'
        ]);
    }

    /** @test */
    public function user_can_update_project_via_api()
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'name' => 'API Updated Project',
            'description' => 'API updated description'
        ];

        $response = $this->putJson("/projects/{$project->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Project updated successfully!'
        ]);
    }

    /** @test */
    public function user_cannot_update_other_users_project()
    {
        $otherUser = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $otherUser->id]);

        $updateData = [
            'name' => 'Unauthorized Update',
            'description' => 'This should fail'
        ];

        $response = $this->put("/projects/{$project->id}", $updateData);

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_delete_their_project()
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $response = $this->delete("/projects/{$project->id}");

        $response->assertStatus(302);
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    /** @test */
    public function user_can_delete_project_via_api()
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/projects/{$project->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Project deleted successfully!'
        ]);
        
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    /** @test */
    public function user_cannot_delete_other_users_project()
    {
        $otherUser = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->delete("/projects/{$project->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('projects', ['id' => $project->id]);
    }

    /** @test */
    public function project_deletion_cascades_to_tasks()
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);
        $task = Task::factory()->create(['project_id' => $project->id]);

        $this->delete("/projects/{$project->id}");

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_projects()
    {
        auth()->logout();

        $response = $this->get('/projects');
        $response->assertRedirect('/login');

        $response = $this->post('/projects', ['name' => 'Test']);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function project_validation_works_correctly()
    {
        // Test empty name
        $response = $this->postJson('/projects', [
            'name' => '',
            'description' => 'Valid description'
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');

        // Test name too long
        $response = $this->postJson('/projects', [
            'name' => str_repeat('a', 256),
            'description' => 'Valid description'
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');

        // Test description too long
        $response = $this->postJson('/projects', [
            'name' => 'Valid name',
            'description' => str_repeat('a', 1001)
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('description');
    }
}