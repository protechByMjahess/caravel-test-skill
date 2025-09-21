<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Project</title>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            max-width: 800px;
            width: 90%;
            background: white;
            border-radius: 25px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
            overflow: hidden;
            animation: slideInUp 0.6s ease;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .header h1 {
            font-size: 3rem;
            margin: 0;
            font-weight: 700;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        
        .header p {
            font-size: 1.2rem;
            margin: 10px 0 0 0;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        .form-container {
            padding: 50px;
        }
        
        .form-group {
            margin-bottom: 30px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 12px;
            color: #2c3e50;
            font-weight: 600;
            font-size: 1.1rem;
            position: relative;
        }
        
        .form-group label::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 30px;
            height: 3px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 2px;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 18px 20px;
            border: 3px solid #e9ecef;
            border-radius: 15px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 5px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }
        
        .form-group textarea {
            height: 150px;
            resize: vertical;
            line-height: 1.6;
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 18px 40px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
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
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.6);
        }
        
        .btn:active {
            transform: translateY(-1px);
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
        }
        
        .btn-secondary:hover {
            box-shadow: 0 12px 35px rgba(108, 117, 125, 0.6);
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
        
        .form-actions {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 40px;
        }
        
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .character-count {
            position: absolute;
            bottom: 8px;
            right: 15px;
            font-size: 12px;
            color: #6c757d;
            background: white;
            padding: 2px 6px;
            border-radius: 10px;
        }
        
        .form-group textarea {
            padding-bottom: 35px;
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
    </style>
</head>
<body>
    <div class="container" x-data="projectForm()">
        <div class="header">
            <h1>✨ Create New Project</h1>
            <p>Bring your ideas to life with a beautiful project</p>
        </div>

        <div class="form-container">
            <div x-show="message" 
                 x-text="message" 
                 :class="messageType === 'success' ? 'success' : 'error'" 
                 class="fade-in"
                 style="display: none;"></div>

            <form @submit.prevent="submitForm()">
                <div class="form-group">
                    <label for="name">🎯 Project Name</label>
                    <input type="text" 
                           id="name" 
                           x-model="form.name" 
                           required
                           placeholder="Enter an amazing project name..."
                           @input="validateForm()"
                           :class="{ 'pulse': form.name.length > 0 }">
                    <div x-show="errors.name" 
                         x-text="errors.name" 
                         class="error fade-in"></div>
                </div>
                
                <div class="form-group">
                    <label for="description">📝 Description</label>
                    <textarea id="description" 
                              x-model="form.description"
                              placeholder="Describe your project... What makes it special? What are your goals?"
                              @input="validateForm()"></textarea>
                    <div class="character-count" x-text="form.description.length + '/500'"></div>
                    <div x-show="errors.description" 
                         x-text="errors.description" 
                         class="error fade-in"></div>
                </div>
                
                <div class="form-actions">
                    <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                        ← Back to Projects
                    </a>
                    <button type="submit" 
                            class="btn" 
                            :disabled="loading || !isFormValid">
                        <span x-show="!loading">
                            🚀 Create Project
                        </span>
                        <span x-show="loading" style="display: flex; align-items: center; gap: 10px;">
                            <div class="loading-spinner"></div>
                            Creating...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function projectForm() {
            return {
                loading: false,
                message: '',
                messageType: '',
                errors: {},
                isFormValid: false,
                form: {
                    name: '',
                    description: ''
                },

                validateForm() {
                    this.isFormValid = this.form.name.trim().length > 0 && 
                                     this.form.name.length <= 255 && 
                                     this.form.description.length <= 500;
                },

                async submitForm() {
                    this.loading = true;
                    this.message = '';
                    this.errors = {};

                    this.showNotification('Creating your amazing project...', 'info');

                    try {
                        const formData = new FormData();
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                        formData.append('name', this.form.name);
                        formData.append('description', this.form.description);

                        const response = await fetch('/projects', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.showNotification('🎉 Project created successfully! Redirecting...', 'success');
                            
                            setTimeout(() => {
                                window.location.href = `/projects/${data.project.id}`;
                            }, 2000);
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

                showNotification(message, type) {
                    this.message = message;
                    this.messageType = type;
                    
                    // Auto-hide success and info messages
                    if (type === 'success' || type === 'info') {
                        setTimeout(() => {
                            this.message = '';
                        }, 5000);
                    }
                }
            }
        }
    </script>
</body>
</html>
