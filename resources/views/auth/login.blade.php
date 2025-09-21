<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .error {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
        }
        .btn {
            background-color: #27ae60;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        .btn:hover {
            background-color: #229954;
        }
        .signup-link {
            text-align: center;
            margin-top: 20px;
        }
        .signup-link a {
            color: #3498db;
            text-decoration: none;
        }
        .signup-link a:hover {
            text-decoration: underline;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .remember-me input {
            width: auto;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container" x-data="loginForm()">
        <h1>Login</h1>
        
        <div x-show="message" x-text="message" :class="messageType === 'success' ? 'success' : 'error'" style="display: none;"></div>

        <form @submit.prevent="submitForm()" :class="{ 'loading': loading }">
            @csrf
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" x-model="form.email" required>
                <div x-show="errors.email" x-text="errors.email" class="error"></div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" x-model="form.password" required>
                <div x-show="errors.password" x-text="errors.password" class="error"></div>
            </div>

            <div class="remember-me">
                <input type="checkbox" id="remember" x-model="form.remember">
                <label for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn" :disabled="loading">
                <span x-show="!loading">Login</span>
                <span x-show="loading">Logging in...</span>
            </button>
        </form>

        <div class="signup-link">
            <p>Don't have an account? <a href="{{ route('signup') }}">Sign up here</a></p>
        </div>
    </div>

    <script>
        function loginForm() {
            return {
                loading: false,
                message: '',
                messageType: '',
                errors: {},
                form: {
                    email: '',
                    password: '',
                    remember: false
                },
                
                async submitForm() {
                    this.loading = true;
                    this.message = '';
                    this.errors = {};
                    
                    try {
                        const formData = new FormData();
                        formData.append('_token', document.querySelector('input[name="_token"]').value);
                        formData.append('email', this.form.email);
                        formData.append('password', this.form.password);
                        if (this.form.remember) {
                            formData.append('remember', '1');
                        }
                        
                        const response = await fetch('/login', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (response.ok) {
                            this.message = 'Login successful! Redirecting...';
                            this.messageType = 'success';
                            
                            // Redirect to dashboard after 2 seconds
                            setTimeout(() => {
                                window.location.href = '/dashboard';
                            }, 2000);
                        } else {
                            if (data.errors) {
                                this.errors = data.errors;
                                this.message = 'Please fix the errors below.';
                                this.messageType = 'error';
                            } else {
                                this.message = data.message || 'Invalid credentials. Please try again.';
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
