<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Project - {{ $project->name }}</title>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        h1 {
            color: #333;
            margin: 0;
        }
        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container" x-data="projectForm()">
        <div class="header">
            <h1>Edit Project</h1>
            <a href="{{ route('projects.show', $project) }}" class="btn">Back to Project</a>
        </div>

        <div x-show="message" x-text="message" :class="messageType === 'success' ? 'success' : 'error'" style="display: none;"></div>

        <form @submit.prevent="submitForm()">
            <div class="form-group">
                <label for="name">Project Name *</label>
                <input type="text" id="name" x-model="form.name" required>
                <div x-show="errors.name" x-text="errors.name" class="error"></div>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" x-model="form.description"></textarea>
                <div x-show="errors.description" x-text="errors.description" class="error"></div>
            </div>
            
            <button type="submit" class="btn btn-success" :disabled="loading">
                <span x-show="!loading">Update Project</span>
                <span x-show="loading">Updating...</span>
            </button>
        </form>
    </div>

    <script>
        function projectForm() {
            return {
                loading: false,
                message: '',
                messageType: '',
                errors: {},
                form: {
                    name: '{{ $project->name }}',
                    description: '{{ $project->description }}'
                },

                async submitForm() {
                    this.loading = true;
                    this.message = '';
                    this.errors = {};

                    try {
                        const formData = new FormData();
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                        formData.append('name', this.form.name);
                        formData.append('description', this.form.description);
                        formData.append('_method', 'PUT');

                        const response = await fetch('/projects/{{ $project->id }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.message = 'Project updated successfully! Redirecting...';
                            this.messageType = 'success';
                            
                            setTimeout(() => {
                                window.location.href = '/projects/{{ $project->id }}';
                            }, 2000);
                        } else {
                            if (data.errors) {
                                this.errors = data.errors;
                                this.message = 'Please fix the errors below.';
                                this.messageType = 'error';
                            } else {
                                this.message = data.message || 'An error occurred.';
                                this.messageType = 'error';
                            }
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.message = 'An error occurred. Please try again.';
                        this.messageType = 'error';
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</body>
</html>
