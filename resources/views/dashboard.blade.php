<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
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
            background-color: #e74c3c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            background-color: #c0392b;
        }
        .user-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .user-info h2 {
            color: #333;
            margin-top: 0;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
            color: #555;
        }
        .info-value {
            color: #333;
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
    <div class="container">
        <div class="header">
            <h1>Dashboard</h1>
            <div>
                <a href="{{ route('projects.index') }}" class="btn" style="background-color: #007bff; margin-right: 10px;">My Projects</a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn">Logout</button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <div class="user-info">
            <h2>Welcome, {{ $user->name }}!</h2>
            
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $user->email }}</div>
            </div>
            
            @if($user->phone)
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">{{ $user->phone }}</div>
            </div>
            @endif
            
            @if($user->date_of_birth)
            <div class="info-row">
                <div class="info-label">Date of Birth:</div>
                <div class="info-value">{{ $user->date_of_birth->format('M d, Y') }}</div>
            </div>
            @endif
            
            @if($user->gender)
            <div class="info-row">
                <div class="info-label">Gender:</div>
                <div class="info-value">{{ ucfirst($user->gender) }}</div>
            </div>
            @endif
            
            @if($user->address)
            <div class="info-row">
                <div class="info-label">Address:</div>
                <div class="info-value">{{ $user->address }}</div>
            </div>
            @endif
            
            @if($user->city)
            <div class="info-row">
                <div class="info-label">City:</div>
                <div class="info-value">{{ $user->city }}</div>
            </div>
            @endif
            
            @if($user->country)
            <div class="info-row">
                <div class="info-label">Country:</div>
                <div class="info-value">{{ $user->country }}</div>
            </div>
            @endif
            
            @if($user->postal_code)
            <div class="info-row">
                <div class="info-label">Postal Code:</div>
                <div class="info-value">{{ $user->postal_code }}</div>
            </div>
            @endif
            
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span style="color: {{ $user->is_active ? '#27ae60' : '#e74c3c' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Member Since:</div>
                <div class="info-value">{{ $user->created_at->format('M d, Y') }}</div>
            </div>
        </div>
    </div>
</body>
</html>
