<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Projects</title>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        // Fallback for Alpine.js
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, Alpine.js should be ready');
            if (typeof Alpine === 'undefined') {
                console.error('Alpine.js failed to load!');
            } else {
                console.log('Alpine.js loaded successfully');
            }
        });
    </script>
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
        
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .project-card {
            border: none;
            border-radius: 20px;
            padding: 25px;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .project-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #667eea, #764ba2);
        }
        
        .project-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .project-card h3 {
            margin: 0 0 15px 0;
            color: #2c3e50;
            font-size: 1.4rem;
            font-weight: 600;
        }
        
        .project-card p {
            color: #6c757d;
            margin: 0 0 20px 0;
            line-height: 1.6;
            font-size: 0.95rem;
        }
        
        .project-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .project-actions .btn {
            padding: 8px 16px;
            font-size: 0.9rem;
        }
        
        .task-count {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            color: #1976d2;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
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
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background: white;
            margin: 10% auto;
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            animation: modalSlideIn 0.3s ease;
        }
        
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #495057;
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-group textarea {
            height: 120px;
            resize: vertical;
        }
        
        .modal-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        
        .close:hover {
            color: #333;
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
        
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body>
    <div class="container" x-data="projectManager()">
        <div class="header">
            <h1>My Projects</h1>
            <div>
                <a href="{{ route('dashboard') }}" class="btn">🏠 Dashboard</a>
                <a href="{{ route('projects.create') }}" class="btn btn-success">➕ Create Project</a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger">🚪 Logout</button>
                </form>
            </div>
        </div>

        <!-- Search and Sort Controls -->
        <div style="display: flex; gap: 20px; margin-bottom: 30px; align-items: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 300px;">
                <input type="text" 
                       x-model="searchQuery" 
                       placeholder="🔍 Search projects..." 
                       style="width: 100%; padding: 12px 16px; border: 2px solid #e9ecef; border-radius: 25px; font-size: 14px; background: #f8f9fa; transition: all 0.3s ease;"
                       @input="clearSearch()"
                       @focus="$event.target.style.borderColor = '#667eea'; $event.target.style.background = 'white'"
                       @blur="$event.target.style.borderColor = '#e9ecef'; $event.target.style.background = '#f8f9fa'">
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <label style="font-weight: 600; color: #495057;">Sort by:</label>
                <select x-model="sortBy" @change="sortProjects(sortBy)" 
                        style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 10px; background: #f8f9fa;">
                    <option value="name">Name</option>
                    <option value="created_at">Date Created</option>
                    <option value="tasks">Task Count</option>
                </select>
                <button @click="sortProjects(sortBy)" 
                        style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 10px; background: #f8f9fa; cursor: pointer; transition: all 0.3s ease;"
                        :title="sortOrder === 'asc' ? 'Sort Ascending' : 'Sort Descending'">
                    <span x-text="sortOrder === 'asc' ? '↑' : '↓'"></span>
                </button>
            </div>
        </div>

        <div x-show="message" x-text="message" :class="messageType === 'success' ? 'success' : 'error'" style="display: none;"></div>

        <div x-show="projects.length === 0" class="empty-state">
            <h3>No projects yet</h3>
            <p>Create your first project to get started!</p>
        </div>

        <div class="projects-grid" x-show="filteredProjects.length > 0" x-transition>
            <template x-for="(project, index) in filteredProjects" :key="project.id">
                <div class="project-card fade-in" 
                     :style="`animation-delay: ${index * 0.1}s`"
                     x-data="{ 
                         isHovered: false,
                         showActions: false 
                     }"
                     @mouseenter="isHovered = true; showActions = true"
                     @mouseleave="isHovered = false; setTimeout(() => showActions = false, 200)">
                    <h3 x-text="project.name" 
                        :class="{ 'pulse': isHovered }"></h3>
                    <p x-text="project.description || 'No description provided'"></p>
                    <div class="task-count" 
                         x-text="`${project.tasks ? project.tasks.length : 0} tasks`"
                         :class="{ 'pulse': isHovered }"></div>
                    <div class="project-actions" 
                         x-show="showActions"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95">
                        <a :href="`/projects/${project.id}`" class="btn" title="View Project">
                            <span>👁️</span> View
                        </a>
                        <button @click="editProject(project)" class="btn" title="Edit Project">
                            <span>✏️</span> Edit
                        </button>
                        <button @click="deleteProject(project.id)" class="btn btn-danger" title="Delete Project">
                            <span>🗑️</span> Delete
                        </button>
                    </div>
                </div>
            </template>
        </div>

    </div>

    <script>
        function projectManager() {
            return {
                projects: @json($projects),
                loading: false,
                message: '',
                messageType: '',
                searchQuery: '',
                sortBy: 'name',
                sortOrder: 'asc',

                get filteredProjects() {
                    let filtered = this.projects;
                    
                    if (this.searchQuery) {
                        filtered = filtered.filter(project => 
                            project.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            (project.description && project.description.toLowerCase().includes(this.searchQuery.toLowerCase()))
                        );
                    }
                    
                    return filtered.sort((a, b) => {
                        let aVal = a[this.sortBy];
                        let bVal = b[this.sortBy];
                        
                        if (this.sortBy === 'tasks') {
                            aVal = a.tasks ? a.tasks.length : 0;
                            bVal = b.tasks ? b.tasks.length : 0;
                        }
                        
                        if (typeof aVal === 'string') {
                            aVal = aVal.toLowerCase();
                            bVal = bVal.toLowerCase();
                        }
                        
                        if (this.sortOrder === 'asc') {
                            return aVal > bVal ? 1 : -1;
                        } else {
                            return aVal < bVal ? 1 : -1;
                        }
                    });
                },

                editProject(project) {
                    window.location.href = `/projects/${project.id}/edit`;
                },

                async deleteProject(projectId) {
                    const project = this.projects.find(p => p.id === projectId);
                    const projectName = project ? project.name : 'this project';
                    
                    if (!confirm(`Are you sure you want to delete "${projectName}"? This will also delete all associated tasks.`)) {
                        return;
                    }

                    this.showNotification('Deleting project...', 'info');

                    try {
                        const response = await fetch(`/projects/${projectId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.projects = this.projects.filter(p => p.id !== projectId);
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
                    
                    // Auto-hide success messages
                    if (type === 'success' || type === 'info') {
                        setTimeout(() => {
                            this.message = '';
                        }, 3000);
                    }
                },

                sortProjects(field) {
                    if (this.sortBy === field) {
                        this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.sortBy = field;
                        this.sortOrder = 'asc';
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
