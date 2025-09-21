<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 500px;
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
        input, select {
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
            background-color: #3498db;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #3498db;
            text-decoration: none;
        }
        .login-link a:hover {
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
    </style>
</head>
<body>
    <div class="container" x-data="signupForm()">
        <h1>Sign Up</h1>
        
        <div x-show="message" x-text="message" :class="messageType === 'success' ? 'success' : 'error'" style="display: none;"></div>

        <form @submit.prevent="submitForm()" :class="{ 'loading': loading }">
            @csrf
            
            <div class="form-group">
                <label for="name">Name *</label>
                <input type="text" id="name" x-model="form.name" required>
                <div x-show="errors.name" x-text="errors.name" class="error"></div>
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" x-model="form.email" required>
                <div x-show="errors.email" x-text="errors.email" class="error"></div>
            </div>

            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" x-model="form.password" required>
                <div x-show="errors.password" x-text="errors.password" class="error"></div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password *</label>
                <input type="password" id="password_confirmation" x-model="form.password_confirmation" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" x-model="form.phone">
                <div x-show="errors.phone" x-text="errors.phone" class="error"></div>
            </div>

            <div class="form-group">
                <label for="date_of_birth">Date of Birth</label>
                <input type="date" id="date_of_birth" x-model="form.date_of_birth">
                <div x-show="errors.date_of_birth" x-text="errors.date_of_birth" class="error"></div>
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" x-model="form.gender">
                    <option value="">Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
                <div x-show="errors.gender" x-text="errors.gender" class="error"></div>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" x-model="form.address">
                <div x-show="errors.address" x-text="errors.address" class="error"></div>
            </div>

            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" x-model="form.city">
                <div x-show="errors.city" x-text="errors.city" class="error"></div>
            </div>

            <div class="form-group">
                <label for="country">Country</label>
                <input type="text" id="country" x-model="form.country">
                <div x-show="errors.country" x-text="errors.country" class="error"></div>
            </div>

            <div class="form-group">
                <label for="postal_code">Postal Code</label>
                <input type="text" id="postal_code" x-model="form.postal_code">
                <div x-show="errors.postal_code" x-text="errors.postal_code" class="error"></div>
            </div>

            <button type="submit" class="btn" :disabled="loading">
                <span x-show="!loading">Sign Up</span>
                <span x-show="loading">Signing Up...</span>
            </button>
        </form>

        <div class="login-link">
            <p>Already have an account? <a href="{{ route('login') }}">Login here</a></p>
        </div>
    </div>

    <script>
        function signupForm() {
            return {
                loading: false,
                message: '',
                messageType: '',
                errors: {},
                form: {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    phone: '',
                    date_of_birth: '',
                    gender: '',
                    address: '',
                    city: '',
                    country: '',
                    postal_code: ''
                },
                
                async submitForm() {
                    this.loading = true;
                    this.message = '';
                    this.errors = {};
                    
                    try {
                        const formData = new FormData();
                        formData.append('_token', document.querySelector('input[name="_token"]').value);
                        Object.keys(this.form).forEach(key => {
                            if (this.form[key]) {
                                formData.append(key, this.form[key]);
                            }
                        });
                        
                        const response = await fetch('/signup', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (response.ok) {
                            this.message = 'Account created successfully! Redirecting...';
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
                                this.message = data.message || 'An error occurred. Please try again.';
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
