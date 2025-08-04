# Documentación de Autenticación API - Laravel Sanctum (V1)

Esta documentación describe cómo usar el sistema de autenticación implementado con Laravel Sanctum para tu SPA (Single Page Application) con versionamiento de API.

## Información de la API

**Base URL:** `http://localhost:8000/api`  
**Versión actual:** `v1`  
**Versiones disponibles:** `v1`

## Endpoints Disponibles

### 1. Registro de Usuario

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

**Respuesta exitosa (201):**

```json
{
    "success": true,
    "message": "Usuario registrado exitosamente",
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
    "meta": {
        "api_version": "v1",
        "timestamp": "2024-01-01T00:00:00.000000Z"
    }
}
```

### 2. Inicio de Sesión

**POST** `/api/v1/login`

**Body:**

```json
{
    "email": "juan@example.com",
    "password": "password123"
}
```

**Respuesta exitosa (200):**

```json
{
    "success": true,
    "message": "Sesión iniciada exitosamente",
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
    "meta": {
        "api_version": "v1",
        "timestamp": "2024-01-01T00:00:00.000000Z"
    }
}
```

### 3. Obtener Información del Usuario

**GET** `/api/v1/user`

**Headers:**

```
Authorization: Bearer 1|abcdef123456...
```

**Respuesta exitosa (200):**

```json
{
    "success": true,
    "message": "Información del usuario obtenida",
    "data": {
        "user": {
            "id": 1,
            "name": "Juan David",
            "email": "juan@example.com",
            "email_verified_at": null,
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    },
    "meta": {
        "api_version": "v1",
        "timestamp": "2024-01-01T00:00:00.000000Z"
    }
}
```

### 4. Verificar Token

**GET** `/api/v1/check`

**Headers:**

```
Authorization: Bearer 1|abcdef123456...
```

**Respuesta exitosa (200):**

```json
{
    "success": true,
    "message": "Token válido",
    "data": {
        "user": {
            "id": 1,
            "name": "Juan David",
            "email": "juan@example.com"
        }
    },
    "meta": {
        "api_version": "v1",
        "timestamp": "2024-01-01T00:00:00.000000Z"
    }
}
```

### 5. Cerrar Sesión

**POST** `/api/v1/logout`

**Headers:**

```
Authorization: Bearer 1|abcdef123456...
```

**Respuesta exitosa (200):**

```json
{
    "success": true,
    "message": "Sesión cerrada exitosamente",
    "data": null,
    "meta": {
        "api_version": "v1",
        "timestamp": "2024-01-01T00:00:00.000000Z"
    }
}
```

## Implementación en Frontend (JavaScript/TypeScript)

### Configuración del Cliente HTTP

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
            throw new Error(data.message || "Error en la petición");
        }

        return data;
    }

    // Métodos de autenticación
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

### Ejemplo de Uso en React/Vue/Angular

```javascript
// Ejemplo con React
import { apiClient } from "./api";

// Registro
const handleRegister = async (userData) => {
    try {
        const response = await apiClient.register(userData);
        console.log("Usuario registrado:", response.data.user);
        // Redirigir al dashboard
    } catch (error) {
        console.error("Error en registro:", error.message);
    }
};

// Login
const handleLogin = async (credentials) => {
    try {
        const response = await apiClient.login(credentials);
        console.log("Usuario logueado:", response.data.user);
        // Redirigir al dashboard
    } catch (error) {
        console.error("Error en login:", error.message);
    }
};

// Logout
const handleLogout = async () => {
    try {
        await apiClient.logout();
        console.log("Sesión cerrada");
        // Redirigir al login
    } catch (error) {
        console.error("Error en logout:", error.message);
    }
};

// Verificar autenticación al cargar la app
const checkAuth = async () => {
    try {
        const response = await apiClient.checkAuth();
        console.log("Usuario autenticado:", response.data.user);
        // Usuario está logueado
    } catch (error) {
        console.log("Usuario no autenticado");
        // Redirigir al login
    }
};
```

## Configuración de CORS

La API está configurada para aceptar peticiones desde cualquier origen. Si necesitas restringir los orígenes, modifica el archivo `config/cors.php`:

```php
'allowed_origins' => [
    'http://localhost:3000',
    'http://localhost:5173',
    'https://tu-dominio.com'
],
```

## Manejo de Errores

La API devuelve errores en formato JSON con códigos de estado HTTP apropiados:

-   **400 Bad Request**: Datos de entrada inválidos
-   **401 Unauthorized**: Credenciales incorrectas o token inválido
-   **422 Unprocessable Entity**: Errores de validación
-   **500 Internal Server Error**: Errores del servidor

Ejemplo de respuesta de error:

```json
{
    "success": false,
    "message": "Las credenciales proporcionadas son incorrectas.",
    "errors": {
        "email": ["Las credenciales proporcionadas son incorrectas."]
    },
    "meta": {
        "api_version": "v1",
        "timestamp": "2024-01-01T00:00:00.000000Z"
    }
}
```

## Seguridad

-   Los tokens se almacenan de forma segura en la base de datos
-   Las contraseñas se hashean usando bcrypt
-   Los tokens se invalidan al cerrar sesión
-   CORS está configurado para permitir credenciales
-   Todas las rutas protegidas requieren autenticación

## Versionamiento

### Estructura de Versiones

-   **V1**: Versión actual (estable)
-   **Legacy**: Endpoints sin versionar (redirigen a V1)

### Migración de Versiones

Para migrar a una nueva versión de la API:

1. Crear nuevos controladores en `app/Http/Controllers/Api/V2/`
2. Agregar nuevas rutas en `routes/api.php` con prefijo `v2`
3. Mantener compatibilidad con versiones anteriores
4. Documentar cambios breaking

### Endpoints Legacy

Los siguientes endpoints siguen funcionando pero redirigen a V1:

-   `POST /api/legacy/register`
-   `POST /api/legacy/login`
-   `POST /api/legacy/logout`
-   `GET /api/legacy/user`
-   `GET /api/legacy/check`

## Próximos Pasos

1. Ejecuta las migraciones: `php artisan migrate`
2. Configura tu archivo `.env` con la URL de tu API
3. Implementa el cliente HTTP en tu SPA
4. Agrega más rutas protegidas según tus necesidades
5. Considera implementar refresh tokens para mayor seguridad
