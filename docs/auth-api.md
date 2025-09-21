# Authentication API Documentation

This document provides instructions on how to test the authentication APIs using Postman.

## Base URL
```
http://127.0.0.1:8000
```

## Authentication Endpoints

### 1. User Signup

**Endpoint:** `POST /signup`

**Description:** Register a new user account

**Headers:**
```
Content-Type: application/x-www-form-urlencoded
Accept: application/json
```

**Body (form-data):**
```
name: John Doe
email: john@example.com
password: password123
password_confirmation: password123
phone: +1234567890
date_of_birth: 1990-01-01
gender: male
address: 123 Main Street
city: New York
country: United States
postal_code: 10001
```

**Required Fields:**
- `name` (string, max 255 characters)
- `email` (string, valid email, unique)
- `password` (string, minimum 8 characters)
- `password_confirmation` (string, must match password)

**Optional Fields:**
- `phone` (string, max 20 characters)
- `date_of_birth` (date, format: YYYY-MM-DD)
- `gender` (enum: male, female, other)
- `address` (string, max 500 characters)
- `city` (string, max 100 characters)
- `country` (string, max 100 characters)
- `postal_code` (string, max 20 characters)

**Success Response:**
- Status: `302 Found` (redirects to dashboard)
- Location: `/dashboard`

**Error Response:**
- Status: `422 Unprocessable Entity`
- Body: Validation errors with field-specific messages

---

### 2. User Login

**Endpoint:** `POST /login`

**Description:** Authenticate user and create session

**Headers:**
```
Content-Type: application/x-www-form-urlencoded
Accept: application/json
```

**Body (form-data):**
```
email: john@example.com
password: password123
remember: true
```

**Required Fields:**
- `email` (string, valid email)
- `password` (string)

**Optional Fields:**
- `remember` (boolean, "remember me" functionality)

**Success Response:**
- Status: `302 Found` (redirects to dashboard)
- Location: `/dashboard`
- Sets session cookie

**Error Response:**
- Status: `422 Unprocessable Entity`
- Body: Validation errors

---

### 3. User Logout

**Endpoint:** `POST /logout`

**Description:** Logout user and destroy session

**Headers:**
```
Content-Type: application/x-www-form-urlencoded
Accept: application/json
Cookie: laravel_session=<session_token>
```

**Body (form-data):**
```
_token: <csrf_token>
```

**Required Fields:**
- `_token` (CSRF token)

**Success Response:**
- Status: `302 Found` (redirects to login)
- Location: `/login`
- Clears session cookie

---

### 4. Dashboard (Protected Route)

**Endpoint:** `GET /dashboard`

**Description:** Access user dashboard (requires authentication)

**Headers:**
```
Accept: application/json
Cookie: laravel_session=<session_token>
```

**Success Response:**
- Status: `200 OK`
- Body: HTML page with user information

**Unauthenticated Response:**
- Status: `302 Found` (redirects to login)
- Location: `/login`

---

## Postman Setup Instructions

### 1. Create a New Collection
1. Open Postman
2. Click "New" → "Collection"
3. Name it "Laravel Auth API"

### 2. Set Collection Variables
1. Go to Collection Settings → Variables
2. Add these variables:
   - `base_url`: `http://127.0.0.1:8000`
   - `session_token`: (leave empty, will be set automatically)

### 3. Create Requests

#### Signup Request
1. Method: `POST`
2. URL: `{{base_url}}/signup`
3. Headers:
   - `Content-Type`: `application/x-www-form-urlencoded`
   - `Accept`: `application/json`
4. Body: Select "x-www-form-urlencoded" and add the form fields

#### Login Request
1. Method: `POST`
2. URL: `{{base_url}}/login`
3. Headers:
   - `Content-Type`: `application/x-www-form-urlencoded`
   - `Accept`: `application/json`
4. Body: Select "x-www-form-urlencoded" and add email/password

#### Dashboard Request
1. Method: `GET`
2. URL: `{{base_url}}/dashboard`
3. Headers:
   - `Accept`: `application/json`
   - `Cookie`: `laravel_session={{session_token}}`

#### Logout Request
1. Method: `POST`
2. URL: `{{base_url}}/logout`
3. Headers:
   - `Content-Type`: `application/x-www-form-urlencoded`
   - `Accept`: `application/json`
   - `Cookie`: `laravel_session={{session_token}}`
4. Body: Add `_token` field

### 4. Handle Session Cookies
1. In Postman Settings → General → Cookies
2. Enable "Automatically follow redirects"
3. Enable "Send cookies automatically"

### 5. Test Flow
1. **Signup**: Create a new user account
2. **Login**: Authenticate with the created account
3. **Dashboard**: Access protected content
4. **Logout**: End the session

## Sample Test Data

### Valid User Registration
```
name: Test User
email: test@example.com
password: password123
password_confirmation: password123
phone: +1234567890
date_of_birth: 1990-01-01
gender: male
address: 123 Test Street
city: Test City
country: Test Country
postal_code: 12345
```

### Valid Login Credentials
```
email: test@example.com
password: password123
remember: true
```

## Error Handling

### Common Validation Errors
- **Email already exists**: `The email has already been taken.`
- **Password too short**: `The password must be at least 8 characters.`
- **Password mismatch**: `The password confirmation does not match.`
- **Invalid email**: `The email must be a valid email address.`
- **Required field missing**: `The [field] field is required.`

### Authentication Errors
- **Invalid credentials**: `Invalid credentials`
- **Session expired**: Redirected to login page
- **CSRF token mismatch**: `The given data was invalid.`

## Notes

- All forms use CSRF protection, so you need to include the `_token` field
- Session cookies are automatically managed by Postman
- The application uses file-based sessions (not database)
- All routes return HTML responses, not JSON (except for validation errors)
- Redirects are handled automatically by Postman when following redirects is enabled
