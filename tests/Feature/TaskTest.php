<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user and project
        $this->user = User::factory()->create();
        $this->project = Project::factory()->create(['user_id' => $this->user->id]);
        $this->actingAs($this->user);
    }

    /** @test */
    public function user_can_create_task_in_their_project()
    {
        $taskData = [
            'title' => 'Test Task',
            'due_date' => now()->addDays(7)->format('Y-m-d')
        ];

        $response = $this->post("/projects/{$this->project->id}/tasks", $taskData);

        $response->assertStatus(302); // Redirect after creation
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'project_id' => $this->project->id,
            'status' => 'todo'
        ]);
    }

    /** @test */
    public function user_can_create_task_via_api()
    {
        $taskData = [
            'title' => 'API Test Task',
            'due_date' => now()->addDays(5)->format('Y-m-d')
        ];

        $response = $this->postJson("/projects/{$this->project->id}/tasks", $taskData);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Task created successfully!'
        ]);
        
        $this->assertDatabaseHas('tasks', [
            'title' => 'API Test Task',
            'project_id' => $this->project->id,
            'status' => 'todo'
        ]);
    }

    /** @test */
    public function task_creation_requires_title()
    {
        $response = $this->post("/projects/{$this->project->id}/tasks", [
            'due_date' => now()->addDays(7)->format('Y-m-d')
        ]);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function task_creation_validates_due_date()
    {
        $response = $this->postJson("/projects/{$this->project->id}/tasks", [
            'title' => 'Test Task',
            'due_date' => 'invalid-date'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('due_date');
    }

    /** @test */
    public function task_creation_validates_due_date_not_in_past()
    {
        $response = $this->postJson("/projects/{$this->project->id}/tasks", [
            'title' => 'Test Task',
            'due_date' => now()->subDays(1)->format('Y-m-d')
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('due_date');
    }

    /** @test */
    public function user_can_update_task_status()
    {
        $task = Task::factory()->create(['project_id' => $this->project->id]);

        $response = $this->putJson("/projects/{$this->project->id}/tasks/{$task->id}", [
            'status' => 'in_progress'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Task updated successfully!'
        ]);
        
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'in_progress'
        ]);
    }

    /** @test */
    public function user_can_update_task_title()
    {
        $task = Task::factory()->create(['project_id' => $this->project->id]);

        $response = $this->putJson("/projects/{$this->project->id}/tasks/{$task->id}", [
            'title' => 'Updated Task Title'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task Title'
        ]);
    }

    /** @test */
    public function user_can_update_task_due_date()
    {
        $task = Task::factory()->create(['project_id' => $this->project->id]);
        $newDueDate = now()->addDays(10)->format('Y-m-d');

        $response = $this->putJson("/projects/{$this->project->id}/tasks/{$task->id}", [
            'due_date' => $newDueDate
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'due_date' => $newDueDate . ' 00:00:00'
        ]);
    }

    /** @test */
    public function user_can_delete_task()
    {
        $task = Task::factory()->create(['project_id' => $this->project->id]);

        $response = $this->deleteJson("/projects/{$this->project->id}/tasks/{$task->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Task deleted successfully!'
        ]);
        
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function user_cannot_create_task_in_other_users_project()
    {
        $otherUser = User::factory()->create();
        $otherProject = Project::factory()->create(['user_id' => $otherUser->id]);

        $taskData = [
            'title' => 'Unauthorized Task',
            'due_date' => now()->addDays(7)->format('Y-m-d')
        ];

        $response = $this->post("/projects/{$otherProject->id}/tasks", $taskData);

        $response->assertStatus(403);
    }

    /** @test */
    public function user_cannot_update_task_in_other_users_project()
    {
        $otherUser = User::factory()->create();
        $otherProject = Project::factory()->create(['user_id' => $otherUser->id]);
        $task = Task::factory()->create(['project_id' => $otherProject->id]);

        $response = $this->putJson("/projects/{$otherProject->id}/tasks/{$task->id}", [
            'title' => 'Unauthorized Update'
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function user_cannot_delete_task_in_other_users_project()
    {
        $otherUser = User::factory()->create();
        $otherProject = Project::factory()->create(['user_id' => $otherUser->id]);
        $task = Task::factory()->create(['project_id' => $otherProject->id]);

        $response = $this->deleteJson("/projects/{$otherProject->id}/tasks/{$task->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function task_status_validation_works()
    {
        $task = Task::factory()->create(['project_id' => $this->project->id]);

        $response = $this->putJson("/projects/{$this->project->id}/tasks/{$task->id}", [
            'status' => 'invalid_status'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('status');
    }

    /** @test */
    public function task_title_validation_works()
    {
        $task = Task::factory()->create(['project_id' => $this->project->id]);

        // Test empty title
        $response = $this->putJson("/projects/{$this->project->id}/tasks/{$task->id}", [
            'title' => ''
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('title');

        // Test title too long
        $response = $this->putJson("/projects/{$this->project->id}/tasks/{$task->id}", [
            'title' => str_repeat('a', 256)
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('title');
    }

    /** @test */
    public function unauthenticated_user_cannot_access_tasks()
    {
        auth()->logout();

        $response = $this->post("/projects/{$this->project->id}/tasks", [
            'title' => 'Test Task'
        ]);
        $response->assertRedirect('/login');

        $task = Task::factory()->create(['project_id' => $this->project->id]);
        $response = $this->put("/projects/{$this->project->id}/tasks/{$task->id}", [
            'title' => 'Updated Task'
        ]);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function task_creation_sets_default_status()
    {
        $response = $this->postJson("/projects/{$this->project->id}/tasks", [
            'title' => 'Test Task'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'status' => 'todo'
        ]);
    }

    /** @test */
    public function task_can_be_created_without_due_date()
    {
        $response = $this->postJson("/projects/{$this->project->id}/tasks", [
            'title' => 'Task without due date'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Task without due date',
            'due_date' => null
        ]);
    }
}