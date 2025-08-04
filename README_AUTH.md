# Sistema de Autenticación - Laravel Sanctum (Versionado)

## 🚀 Configuración Rápida

### 1. Ejecutar Migraciones

```bash
php artisan migrate
```

### 2. Iniciar el Servidor

```bash
php artisan serve
```

### 3. Probar la API

Abre el archivo `test_api.html` en tu navegador para probar todos los endpoints.

## 📋 Endpoints Disponibles

| Método | Endpoint           | Descripción       | Autenticación |
| ------ | ------------------ | ----------------- | ------------- |
| POST   | `/api/v1/register` | Registrar usuario | No            |
| POST   | `/api/v1/login`    | Iniciar sesión    | No            |
| GET    | `/api/v1/user`     | Obtener usuario   | Sí            |
| GET    | `/api/v1/check`    | Verificar token   | Sí            |
| POST   | `/api/v1/logout`   | Cerrar sesión     | Sí            |

## 🏗️ Estructura del Proyecto

```
app/Http/Controllers/
├── Api/
│   ├── ApiController.php          # Controlador base para API
│   └── V1/
│       └── AuthController.php     # Controlador de autenticación V1
```

## 🔧 Configuración

### Variables de Entorno (.env)

```env
APP_URL=http://localhost:8000
SANCTUM_STATEFUL_DOMAINS=localhost:3000,localhost:5173
SESSION_DOMAIN=localhost
```

### CORS (config/cors.php)

Ya configurado para permitir peticiones desde cualquier origen. Para producción, restringe los orígenes:

```php
'allowed_origins' => [
    'http://localhost:3000',
    'http://localhost:5173',
    'https://tu-dominio.com'
],
```

## 📱 Uso en Frontend

### Cliente HTTP Básico

```javascript
const API_BASE_URL = "http://localhost:8000/api/v1";

class ApiClient {
    constructor() {
        this.token = localStorage.getItem("auth_token");
    }

    setToken(token) {
        this.token = token;
        localStorage.setItem("auth_token", token);
    }

    async request(endpoint, options = {}) {
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

        const response = await fetch(`${API_BASE_URL}${endpoint}`, config);
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
            this.token = null;
            localStorage.removeItem("auth_token");
        }
    }

    async getUser() {
        return await this.request("/user");
    }
}

export const apiClient = new ApiClient();
```

### Ejemplo de Uso

```javascript
// Registro
const userData = {
    name: "Juan David",
    email: "juan@example.com",
    password: "password123",
    password_confirmation: "password123",
};

try {
    const response = await apiClient.register(userData);
    console.log("Usuario registrado:", response.data.user);
} catch (error) {
    console.error("Error:", error.message);
}

// Login
const credentials = {
    email: "juan@example.com",
    password: "password123",
};

try {
    const response = await apiClient.login(credentials);
    console.log("Usuario logueado:", response.data.user);
} catch (error) {
    console.error("Error:", error.message);
}
```

## 🛡️ Seguridad

-   **Tokens**: Se almacenan de forma segura en la base de datos
-   **Contraseñas**: Se hashean con bcrypt
-   **CORS**: Configurado para permitir credenciales
-   **Validación**: Todos los inputs se validan
-   **Middleware**: Rutas protegidas requieren autenticación

## 🔍 Debugging

### Verificar Token

```bash
curl -H "Authorization: Bearer TU_TOKEN" http://localhost:8000/api/v1/check
```

### Ver Usuario

```bash
curl -H "Authorization: Bearer TU_TOKEN" http://localhost:8000/api/v1/user
```

### Información de la API

```bash
curl http://localhost:8000/api/
```

## 📝 Versionamiento

### Estructura de Versiones

-   **V1**: Versión actual (estable)
-   **Legacy**: Endpoints sin versionar (redirigen a V1)

### Agregar Nueva Versión

1. Crear carpeta `app/Http/Controllers/Api/V2/`
2. Crear controladores en la nueva versión
3. Agregar rutas en `routes/api.php` con prefijo `v2`
4. Actualizar documentación

### Ejemplo de Nueva Versión

```php
// app/Http/Controllers/Api/V2/AuthController.php
namespace App\Http\Controllers\Api\V2;

class AuthController extends ApiController
{
    // Implementación de V2
}

// routes/api.php
Route::prefix('v2')->group(function () {
    Route::post('/register', [V2\AuthController::class, 'register']);
    // ... más rutas
});
```

## 🐛 Solución de Problemas

### Error 419 (CSRF Token)

-   Asegúrate de que CORS esté configurado correctamente
-   Verifica que `supports_credentials` esté en `true`

### Error 401 (Unauthorized)

-   Verifica que el token sea válido
-   Asegúrate de incluir el header `Authorization: Bearer TOKEN`

### Error 422 (Validation Error)

-   Revisa los datos enviados
-   Verifica que las contraseñas coincidan en el registro

### Error 404 (Not Found)

-   Verifica que estés usando la URL correcta con versión
-   Asegúrate de que la ruta exista en la versión especificada

## 📚 Recursos Adicionales

-   [Documentación de Laravel Sanctum](https://laravel.com/docs/sanctum)
-   [Guía de CORS](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS)
-   [Mejores Prácticas de Seguridad](https://owasp.org/www-project-top-ten/)
-   [API Versioning Best Practices](https://restfulapi.net/versioning/)
