# API Authentication Documentation - Laravel Sanctum (V1)

This documentation describes how to use the authentication system implemented with Laravel Sanctum for your SPA (Single Page Application) with API versioning.

## API Information

**Base URL:** `http://localhost:8000/api`  
**Current Version:** `v1`  
**Available Versions:** `v1`

## Available Endpoints

### 1. User Registration

**POST** `/api/v1/register`

**Body:**

```json
{
    "name": "Juan David",
    "email": "juan@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Successful Response (201):**

```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "Juan David",
            "email": "juan@example.com",
            "email_verified_at": null,
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        },
        "token": "1|abcdef123456..."
    },
    "status_code": 201,
    "meta": {
        "api_version": "v1",
        "timestamp": "2024-01-01T00:00:00.000000Z"
    }
}
```

### 2. User Login

**POST** `/api/v1/login`

**Body:**

```json
{
    "email": "juan@example.com",
    "password": "password123"
}
```

**Successful Response (200):**

```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "Juan David",
            "email": "juan@example.com",
            "email_verified_at": null,
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        },
        "token": "1|abcdef123456..."
    },
    "status_code": 200,
    "meta": {
        "api_version": "v1",
        "timestamp": "2024-01-01T00:00:00.000000Z"
    }
}
```

### 3. Get User Information

**GET** `/api/v1/user`

**Headers:**

```
Authorization: Bearer 1|abcdef123456...
```

**Successful Response (200):**

```json
{
    "success": true,
    "message": "User information retrieved successfully",
    "data": {
        "id": 1,
        "name": "Juan David",
        "email": "juan@example.com",
        "email_verified_at": null,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    "status_code": 200,
    "meta": {
        "api_version": "v1",
        "timestamp": "2024-01-01T00:00:00.000000Z"
    }
}
```

### 4. Check Token

**GET** `/api/v1/check`

**Headers:**

```
Authorization: Bearer 1|abcdef123456...
```

**Successful Response (200):**

```json
{
    "success": true,
    "message": "Token is valid",
    "data": {
        "id": 1,
        "name": "Juan David",
        "email": "juan@example.com",
        "email_verified_at": null,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    "status_code": 200,
    "meta": {
        "api_version": "v1",
        "timestamp": "2024-01-01T00:00:00.000000Z"
    }
}
```

### 5. Logout

**POST** `/api/v1/logout`

**Headers:**

```
Authorization: Bearer 1|abcdef123456...
```

**Successful Response (200):**

```json
{
    "success": true,
    "message": "Logout successful",
    "data": null,
    "status_code": 200,
    "meta": {
        "api_version": "v1",
        "timestamp": "2024-01-01T00:00:00.000000Z"
    }
}
```

## Frontend Implementation (JavaScript/TypeScript)

### HTTP Client Configuration

```javascript
// api.js
const API_BASE_URL = "http://localhost:8000/api/v1";

class ApiClient {
    constructor() {
        this.baseURL = API_BASE_URL;
        this.token = localStorage.getItem("auth_token");
    }

    setToken(token) {
        this.token = token;
        localStorage.setItem("auth_token", token);
    }

    clearToken() {
        this.token = null;
        localStorage.removeItem("auth_token");
    }

    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            ...options,
        };

        if (this.token) {
            config.headers.Authorization = `Bearer ${this.token}`;
        }

        const response = await fetch(url, config);
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || "Request error");
        }

        return data;
    }

    // Authentication methods
    async register(userData) {
        const response = await this.request("/register", {
            method: "POST",
            body: JSON.stringify(userData),
        });

        if (response.data?.token) {
            this.setToken(response.data.token);
        }

        return response;
    }

    async login(credentials) {
        const response = await this.request("/login", {
            method: "POST",
            body: JSON.stringify(credentials),
        });

        if (response.data?.token) {
            this.setToken(response.data.token);
        }

        return response;
    }

    async logout() {
        try {
            await this.request("/logout", { method: "POST" });
        } finally {
            this.clearToken();
        }
    }

    async getUser() {
        return await this.request("/user");
    }

    async checkAuth() {
        return await this.request("/check");
    }
}

export const apiClient = new ApiClient();
```

### Usage Example in React/Vue/Angular

```javascript
// Example with React
import { apiClient } from "./api";

// Registration
const handleRegister = async (userData) => {
    try {
        const response = await apiClient.register(userData);
        console.log("User registered:", response.data.user);
        console.log("Status code:", response.status_code);
        // Redirect to dashboard
    } catch (error) {
        console.error("Registration error:", error.message);
    }
};

// Login
const handleLogin = async (credentials) => {
    try {
        const response = await apiClient.login(credentials);
        console.log("User logged in:", response.data.user);
        console.log("Status code:", response.status_code);
        // Redirect to dashboard
    } catch (error) {
        console.error("Login error:", error.message);
    }
};

// Logout
const handleLogout = async () => {
    try {
        await apiClient.logout();
        console.log("Session closed");
        // Redirect to login
    } catch (error) {
        console.error("Logout error:", error.message);
    }
};

// Check authentication on app load
const checkAuth = async () => {
    try {
        const response = await apiClient.checkAuth();
        console.log("User authenticated:", response.data);
        console.log("Status code:", response.status_code);
        // User is logged in
    } catch (error) {
        console.log("User not authenticated");
        // Redirect to login
    }
};
```

## CORS Configuration

The API is configured to accept requests from any origin. If you need to restrict origins, modify the file `config/cors.php`:

```php
'allowed_origins' => [
    'http://localhost:3000',
    'http://localhost:5173',
    'https://your-domain.com'
],
```

## Error Handling

The API returns errors in JSON format with appropriate HTTP status codes:

-   **400 Bad Request**: Invalid input data
-   **401 Unauthorized**: Incorrect credentials or invalid token
-   **422 Unprocessable Entity**: Validation errors
-   **500 Internal Server Error**: Server errors

Example error response:

```json
{
    "success": false,
    "message": "Invalid credentials provided.",
    "errors": {
        "email": ["Invalid credentials provided."]
    },
    "status_code": 401,
    "meta": {
        "api_version": "v1",
        "timestamp": "2024-01-01T00:00:00.000000Z"
    }
}
```

## Security

-   Tokens are stored securely in the database
-   Passwords are hashed using bcrypt
-   Tokens are invalidated on logout
-   CORS is configured to allow credentials
-   All protected routes require authentication

## Versioning

### Version Structure

-   **V1**: Current version (stable)
-   **Legacy**: Unversioned endpoints (redirect to V1)

### Version Migration

To migrate to a new API version:

1. Create new controllers in `app/Http/Controllers/Api/V2/`
2. Add new routes in `routes/api.php` with `v2` prefix
3. Maintain compatibility with previous versions
4. Document breaking changes

### Legacy Endpoints

The following endpoints still work but redirect to V1:

-   `POST /api/legacy/register`
-   `POST /api/legacy/login`
-   `POST /api/legacy/logout`
-   `GET /api/legacy/user`
-   `GET /api/legacy/check`

## Project Structure

```
app/Http/
├── Controllers/
│   └── Api/
│       ├── ApiController.php          # Base API controller
│       └── V1/
│           └── AuthController.php     # V1 authentication controller
├── Requests/
│   └── Api/
│       └── V1/
│           ├── LoginRequest.php       # Login validation
│           └── RegisterRequest.php    # Registration validation
└── Resources/
    └── Api/
        └── V1/
            ├── AuthResource.php       # Authentication response resource
            └── UserResource.php       # User response resource
```

## Response Structure

All API responses follow a consistent structure:

```json
{
    "success": true/false,
    "message": "Descriptive message",
    "data": { ... },
    "status_code": 200,
    "meta": {
        "api_version": "v1",
        "timestamp": "2024-01-01T00:00:00.000000Z"
    }
}
```

## Next Steps

1. Run migrations: `php artisan migrate`
2. Configure your `.env` file with your API URL
3. Implement the HTTP client in your SPA
4. Add more protected routes as needed
5. Consider implementing refresh tokens for enhanced security
