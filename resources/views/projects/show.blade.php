<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $project->name }} - Tasks</title>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .project-description {
            color: #6c757d;
            margin: 15px 0 25px 0;
            font-style: italic;
            font-size: 1.1rem;
            line-height: 1.6;
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);
        }
        
        .btn-danger:hover {
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.6);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #51cf66, #40c057);
            box-shadow: 0 4px 15px rgba(81, 207, 102, 0.4);
        }
        
        .btn-success:hover {
            box-shadow: 0 8px 25px rgba(81, 207, 102, 0.6);
        }
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 0.9rem;
        }
        
        .task-form {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 25px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 2px solid #e9ecef;
        }
        
        .task-form h3 {
            margin: 0 0 20px 0;
            color: #2c3e50;
            font-size: 1.3rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #495057;
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-row {
            display: flex;
            gap: 20px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .tasks-list {
            margin-top: 30px;
        }
        
        .tasks-list h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        
        .task-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border: none;
            border-radius: 15px;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .task-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(135deg, #667eea, #764ba2);
        }
        
        .task-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .task-info {
            flex: 1;
            margin-right: 20px;
        }
        
        .task-title {
            font-weight: 600;
            color: #2c3e50;
            margin: 0 0 8px 0;
            font-size: 1.1rem;
        }
        
        .task-meta {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .task-meta span[title] {
            transition: all 0.3s ease;
        }
        
        .task-meta span[title]:hover {
            transform: scale(1.05);
            text-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
        }
        
        .task-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-todo {
            background: linear-gradient(135deg, #e9ecef, #dee2e6);
            color: #6c757d;
        }
        
        .status-in_progress {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            color: #856404;
        }
        
        .status-done {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }
        
        .task-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        
        .task-actions .btn {
            padding: 6px 12px;
            font-size: 0.8rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #495057;
        }
        
        .error {
            background: linear-gradient(135deg, #ffebee, #ffcdd2);
            color: #c62828;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #f44336;
            animation: shake 0.5s ease;
        }
        
        .success {
            background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
            color: #2e7d32;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #4caf50;
            animation: slideInDown 0.5s ease;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        
        .task-item.completed {
            opacity: 0.7;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .task-item.completed .task-title {
            text-decoration: line-through;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container" x-data="taskManager()">
        <div class="header">
            <div>
                <h1>{{ $project->name }}</h1>
                <div class="project-description">{{ $project->description ?: 'No description' }}</div>
            </div>
            <div>
                <a href="{{ route('projects.index') }}" class="btn">Back to Projects</a>
                <a href="{{ route('projects.edit', $project) }}" class="btn">Edit Project</a>
            </div>
        </div>

        <div x-show="message" x-text="message" :class="messageType === 'success' ? 'success' : 'error'" style="display: none;"></div>

        <div class="task-form">
            <h3>Add New Task</h3>
            <form @submit.prevent="addTask()">
                <div class="form-row">
                    <div class="form-group">
                        <label for="title">Task Title *</label>
                        <input type="text" id="title" x-model="form.title" required>
                        <div x-show="errors.title" x-text="errors.title" class="error"></div>
                    </div>
                    <div class="form-group">
                        <label for="due_date">Due Date</label>
                        <input type="date" id="due_date" x-model="form.due_date">
                        <div x-show="errors.due_date" x-text="errors.due_date" class="error"></div>
                    </div>
                </div>
                <button type="submit" class="btn btn-success" :disabled="loading">
                    <span x-show="!loading">Add Task</span>
                    <span x-show="loading">Adding...</span>
                </button>
            </form>
        </div>

        <!-- Task Search and Filter Controls -->
        <div style="display: flex; gap: 20px; margin-bottom: 30px; align-items: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 300px;">
                <input type="text" 
                       x-model="searchQuery" 
                       placeholder="🔍 Search tasks..." 
                       style="width: 100%; padding: 12px 16px; border: 2px solid #e9ecef; border-radius: 25px; font-size: 14px; background: #f8f9fa; transition: all 0.3s ease;"
                       @focus="$event.target.style.borderColor = '#667eea'; $event.target.style.background = 'white'"
                       @blur="$event.target.style.borderColor = '#e9ecef'; $event.target.style.background = '#f8f9fa'">
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <label style="font-weight: 600; color: #495057;">Filter by status:</label>
                <select x-model="statusFilter" 
                        style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 10px; background: #f8f9fa;">
                    <option value="all">All Tasks</option>
                    <option value="todo">📋 To Do</option>
                    <option value="in_progress">🔄 In Progress</option>
                    <option value="done">✅ Done</option>
                </select>
            </div>
        </div>

        <div class="tasks-list">
            <h3>Tasks (<span x-text="filteredTasks.length"></span> of <span x-text="tasks.length"></span>)</h3>
            
            <div x-show="filteredTasks.length === 0 && tasks.length > 0" class="empty-state">
                <h3>🔍 No tasks found</h3>
                <p>Try adjusting your search or filter criteria.</p>
            </div>
            
            <div x-show="tasks.length === 0" class="empty-state">
                <h3>📝 No tasks yet</h3>
                <p>Add your first task above to get started!</p>
            </div>

            <template x-for="(task, index) in filteredTasks" :key="task.id">
                <div class="task-item fade-in" 
                     :class="{ 'completed': task.status === 'done' }"
                     :style="`animation-delay: ${index * 0.1}s`"
                     x-data="{ 
                         isHovered: false,
                         showActions: false,
                         isEditing: false,
                         isEditingDueDate: false,
                         editTitle: task.title,
                         editDueDate: task.due_date
                     }"
                     @mouseenter="isHovered = true; showActions = true"
                     @mouseleave="isHovered = false; setTimeout(() => showActions = false, 200)">
                    
                    <div class="task-info">
                        <div x-show="!isEditing">
                            <div class="task-title" x-text="task.title"></div>
                            <div class="task-meta">
                                <span x-show="task.due_date" x-text="`📅 Due: ${formatDate(task.due_date)}`" 
                                      @click="isEditingDueDate = true; editDueDate = task.due_date" 
                                      style="cursor: pointer; text-decoration: underline; color: #667eea;"
                                      title="Click to edit due date"></span>
                                <span x-show="!task.due_date" @click="isEditingDueDate = true; editDueDate = ''" 
                                      style="cursor: pointer; color: #6c757d; font-style: italic;"
                                      title="Click to add due date">📅 Add due date</span>
                                <span x-show="task.due_date && task.created_at"> • </span>
                                <span x-text="`📅 Created: ${formatDate(task.created_at)}`"></span>
                            </div>
                        </div>
                        <div x-show="isEditing" style="display: flex; gap: 10px; align-items: center;">
                            <input type="text" 
                                   x-model="editTitle" 
                                   @keyup.enter="saveTaskEdit(task.id)"
                                   @keyup.escape="isEditing = false; editTitle = task.title"
                                   style="flex: 1; padding: 8px 12px; border: 2px solid #667eea; border-radius: 8px; font-size: 14px;">
                            <button @click="saveTaskEdit(task.id)" class="btn btn-sm btn-success">✓</button>
                            <button @click="isEditing = false; editTitle = task.title" class="btn btn-sm">✕</button>
                        </div>
                        <div x-show="isEditingDueDate" style="display: flex; gap: 10px; align-items: center; margin-top: 8px;">
                            <input type="date" 
                                   x-model="editDueDate" 
                                   @keyup.enter="saveTaskDueDate(task.id)"
                                   @keyup.escape="isEditingDueDate = false; editDueDate = task.due_date"
                                   style="padding: 8px 12px; border: 2px solid #667eea; border-radius: 8px; font-size: 14px;">
                            <button @click="saveTaskDueDate(task.id)" class="btn btn-sm btn-success">✓</button>
                            <button @click="isEditingDueDate = false; editDueDate = task.due_date" class="btn btn-sm">✕</button>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <select @change="updateTaskStatus(task.id, $event.target.value)" 
                                :value="task.status"
                                :class="`status-${task.status}`"
                                style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 20px; background: white; font-weight: 600; cursor: pointer; transition: all 0.3s ease;"
                                @focus="$event.target.style.borderColor = '#667eea'"
                                @blur="$event.target.style.borderColor = '#e9ecef'">
                            <option value="todo">📋 To Do</option>
                            <option value="in_progress">🔄 In Progress</option>
                            <option value="done">✅ Done</option>
                        </select>
                        
                        <div class="task-actions" 
                             x-show="showActions"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95">
                            <button @click="isEditing = true" class="btn btn-sm" title="Edit Task">
                                ✏️ Edit
                            </button>
                            <button @click="deleteTask(task.id)" class="btn btn-sm btn-danger" title="Delete Task">
                                🗑️ Delete
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <script>
        function taskManager() {
            return {
                tasks: @json($project->tasks),
                loading: false,
                message: '',
                messageType: '',
                errors: {},
                form: {
                    title: '',
                    due_date: ''
                },
                searchQuery: '',
                statusFilter: 'all',

                get filteredTasks() {
                    let filtered = this.tasks;
                    
                    if (this.searchQuery) {
                        filtered = filtered.filter(task => 
                            task.title.toLowerCase().includes(this.searchQuery.toLowerCase())
                        );
                    }
                    
                    if (this.statusFilter !== 'all') {
                        filtered = filtered.filter(task => task.status === this.statusFilter);
                    }
                    
                    return filtered.sort((a, b) => {
                        // Sort by status: todo, in_progress, done
                        const statusOrder = { 'todo': 1, 'in_progress': 2, 'done': 3 };
                        return statusOrder[a.status] - statusOrder[b.status];
                    });
                },

                formatDate(dateString) {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric' 
                    });
                },

                async addTask() {
                    this.loading = true;
                    this.message = '';
                    this.errors = {};

                    this.showNotification('Adding task...', 'info');

                    try {
                        const formData = new FormData();
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                        formData.append('title', this.form.title);
                        formData.append('due_date', this.form.due_date);

                        const response = await fetch('/projects/{{ $project->id }}/tasks', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.showNotification(data.message, 'success');
                            this.tasks.unshift(data.task);
                            this.form.title = '';
                            this.form.due_date = '';
                        } else {
                            if (data.errors) {
                                this.errors = data.errors;
                                this.showNotification('Please fix the errors below', 'error');
                            } else {
                                this.showNotification(data.message || 'An error occurred', 'error');
                            }
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.showNotification('An error occurred. Please try again.', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                async updateTaskStatus(taskId, status) {
                    const task = this.tasks.find(t => t.id === taskId);
                    if (!task) return;

                    this.showNotification(`Updating task status to ${status}...`, 'info');

                    try {
                        const formData = new FormData();
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                        formData.append('status', status);
                        formData.append('_method', 'PUT');

                        const response = await fetch(`/projects/{{ $project->id }}/tasks/${taskId}`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok) {
                            task.status = status;
                            this.showNotification(data.message, 'success');
                        } else {
                            this.showNotification(data.message || 'An error occurred', 'error');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.showNotification('An error occurred. Please try again.', 'error');
                    }
                },

                async deleteTask(taskId) {
                    const task = this.tasks.find(t => t.id === taskId);
                    const taskTitle = task ? task.title : 'this task';
                    
                    if (!confirm(`Are you sure you want to delete "${taskTitle}"?`)) {
                        return;
                    }

                    this.showNotification('Deleting task...', 'info');

                    try {
                        const response = await fetch(`/projects/{{ $project->id }}/tasks/${taskId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.tasks = this.tasks.filter(t => t.id !== taskId);
                            this.showNotification(data.message, 'success');
                        } else {
                            this.showNotification(data.message || 'An error occurred', 'error');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.showNotification('An error occurred. Please try again.', 'error');
                    }
                },

                async saveTaskEdit(taskId) {
                    const task = this.tasks.find(t => t.id === taskId);
                    if (!task) return;

                    const editInput = document.querySelector(`input[x-model="editTitle"]`);
                    const newTitle = editInput ? editInput.value.trim() : '';

                    if (!newTitle || newTitle === task.title) {
                        return;
                    }

                    this.showNotification('Updating task...', 'info');

                    try {
                        const formData = new FormData();
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                        formData.append('title', newTitle);
                        formData.append('_method', 'PUT');

                        const response = await fetch(`/projects/{{ $project->id }}/tasks/${taskId}`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok) {
                            task.title = newTitle;
                            this.showNotification(data.message, 'success');
                        } else {
                            this.showNotification(data.message || 'An error occurred', 'error');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.showNotification('An error occurred. Please try again.', 'error');
                    }
                },

                async saveTaskDueDate(taskId) {
                    const task = this.tasks.find(t => t.id === taskId);
                    if (!task) return;

                    const editInput = document.querySelector(`input[x-model="editDueDate"]`);
                    const newDueDate = editInput ? editInput.value : '';

                    if (newDueDate === task.due_date) {
                        return;
                    }

                    this.showNotification('Updating due date...', 'info');

                    try {
                        const formData = new FormData();
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                        formData.append('due_date', newDueDate);
                        formData.append('_method', 'PUT');

                        const response = await fetch(`/projects/{{ $project->id }}/tasks/${taskId}`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok) {
                            task.due_date = newDueDate;
                            this.showNotification(data.message, 'success');
                        } else {
                            this.showNotification(data.message || 'An error occurred', 'error');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.showNotification('An error occurred. Please try again.', 'error');
                    }
                },

                showNotification(message, type) {
                    this.message = message;
                    this.messageType = type;
                    
                    // Auto-hide success and info messages
                    if (type === 'success' || type === 'info') {
                        setTimeout(() => {
                            this.message = '';
                        }, 3000);
                    }
                },

                clearSearch() {
                    this.searchQuery = '';
                }
            }
        }
    </script>
</body>
</html>
