<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes to users table
        Schema::table('users', function (Blueprint $table) {
            // Index for filtering active users
            $table->index('is_active');
            
            // Index for sorting by creation date
            $table->index('created_at');
            
            // Composite index for common user queries
            $table->index(['is_active', 'created_at']);
        });

        // Add indexes to projects table
        Schema::table('projects', function (Blueprint $table) {
            // Index for user-specific project queries (most common query)
            $table->index('user_id');
            
            // Index for project name searches
            $table->index('name');
            
            // Index for sorting by creation date
            $table->index('created_at');
            
            // Index for sorting by last update
            $table->index('updated_at');
            
            // Composite index for user projects with sorting
            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'updated_at']);
            
            // Composite index for search functionality
            $table->index(['user_id', 'name']);
        });

        // Add indexes to tasks table
        Schema::table('tasks', function (Blueprint $table) {
            // Index for project-specific task queries
            $table->index('project_id');
            
            // Index for status filtering (very common query)
            $table->index('status');
            
            // Index for due date queries
            $table->index('due_date');
            
            // Index for sorting by creation date
            $table->index('created_at');
            
            // Composite indexes for common task queries
            $table->index(['project_id', 'status']);
            $table->index(['project_id', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index(['project_id', 'status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['is_active', 'created_at']);
        });

        // Drop indexes from projects table
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['name']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['user_id', 'updated_at']);
            $table->dropIndex(['user_id', 'name']);
        });

        // Drop indexes from tasks table
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['project_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['due_date']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['project_id', 'status']);
            $table->dropIndex(['project_id', 'created_at']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['project_id', 'status', 'created_at']);
        });
    }
};
