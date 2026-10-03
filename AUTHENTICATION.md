# DevNova Gym Management System (GMS) API

A RESTful API for managing gym operations, built with **Laravel 12**, **PHP 8.2+**, **MySQL**, and **JWT Authentication**.

---

## Tech Stack

- PHP 8.2+
- Laravel 12
- MySQL
- JWT Authentication
- `php-open-source-saver/jwt-auth`
- RESTful API

---

# API Base URL

```text
/api
```

---

# Authentication

The API uses two types of tokens:

### Access Token

- JWT
- Sent in the `Authorization` header.
- Lifetime: **30 minutes**
- Used to access protected endpoints.

```http
Authorization: Bearer <access_token>
```

### Refresh Token

- Stored in the database as a **SHA-256 hash**.
- The raw token is stored on the client inside an **HttpOnly Cookie**.
- Lifetime: **14 days**.
- It is **not returned inside the JSON response**.
- Refresh tokens are rotated whenever `/auth/refresh` is called.

Cookie:

```text
refresh_token
```

The cookie is configured as:

```text
HttpOnly: true
Secure: true in production
SameSite: Lax
Path: /
```

---

# Common Headers

For authenticated requests:

```http
Authorization: Bearer <access_token>
Content-Type: application/json
Accept: application/json
```

For endpoints that do not require the Access Token:

```http
Content-Type: application/json
Accept: application/json
```

---

# Standard Response Format

### Success

```json
{
    "success": true,
    "message": "Operation successful.",
    "data": {}
}
```

### Error

```json
{
    "success": false,
    "message": "Error message."
}
```

### Validation Error

```json
{
    "success": false,
    "message": "Validation failed.",
    "errors": {
        "email": [
            "The email field is required when phone is not present."
        ]
    }
}
```

---

# Authentication Endpoints

## 1. Login

Authenticates a user using either email or phone number and password.

### Endpoint

```http
POST /api/auth/login
```

### Authentication

No authentication required.

### Rate Limit

Maximum **5 login attempts per minute** per identifier + IP address.

---

### Request — Email

```json
{
    "email": "user@example.com",
    "password": "password"
}
```

### Request — Phone

```json
{
    "phone": "01000000000",
    "password": "password"
}
```

---

### Successful Response

**HTTP 200**

```json
{
    "success": true,
    "message": "Login successful.",
    "data": {
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "bearer",
        "expires_in": 1800,
        "gym_id": 1,
        "role": "receptionist"
    }
}
```

### Refresh Token

The login response also sets the following HttpOnly cookie:

```text
refresh_token=<secure-random-token>
```

The refresh token is intentionally **not included in the JSON response**.

---

### Validation Error

**HTTP 422**

Example: neither email nor phone is provided.

```json
{
    "success": false,
    "message": "Validation failed.",
    "errors": {
        "email": [
            "The email field is required when phone is not present."
        ],
        "phone": [
            "The phone field is required when email is not present."
        ]
    }
}
```

---

### Invalid Credentials

**HTTP 401**

```json
{
    "success": false,
    "message": "Invalid credentials."
}
```

---

### Rate Limit Exceeded

**HTTP 429**

```json
{
    "success": false,
    "message": "Too many login attempts. Please try again later.",
    "retry_after": 60
}
```

---

# 2. Refresh Access Token

Generates a new Access Token using the Refresh Token stored in the HttpOnly Cookie.

### Endpoint

```http
POST /api/auth/refresh
```

### Authentication

No `Authorization` header is required.

The request is authenticated using:

```text
refresh_token
```

HttpOnly Cookie.

---

### Request

No request body is required.

```json
{}
```

The browser/client automatically sends the Refresh Token Cookie.

---

### Successful Response

**HTTP 200**

```json
{
    "success": true,
    "message": "Token refreshed successfully.",
    "data": {
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "bearer",
        "expires_in": 1800
    }
}
```

A **new Refresh Token** is also returned through the HttpOnly Cookie.

The previous Refresh Token is deleted from the database.

---

### Missing Refresh Token

**HTTP 401**

```json
{
    "success": false,
    "message": "Refresh token is missing."
}
```

---

### Invalid or Expired Refresh Token

**HTTP 401**

```json
{
    "success": false,
    "message": "Invalid or expired refresh token."
}
```

---

## Refresh Token Rotation

Every successful refresh performs token rotation:

```text
Old Refresh Token
        ↓
Validate
        ↓
Delete Old Token
        ↓
Generate New Refresh Token
        ↓
Store New Token Hash
        ↓
Set New HttpOnly Cookie
```

This prevents the same Refresh Token from being reused after a successful refresh.

---

# 3. Logout

Logs the user out and invalidates both authentication mechanisms.

### Endpoint

```http
POST /api/auth/logout
```

### Authentication

Requires a valid Access Token.

```http
Authorization: Bearer <access_token>
```

---

### Request

No request body is required.

```json
{}
```

---

### Successful Response

**HTTP 200**

```json
{
    "success": true,
    "message": "Logout successful.",
    "data": null
}
```

---

### Logout Process

When logout is performed:

1. The current JWT Access Token is blacklisted.
2. The Refresh Token is removed from the database.
3. The `refresh_token` HttpOnly Cookie is cleared.

```text
Access Token
     ↓
Blacklist

Refresh Token
     ↓
Delete from Database
     ↓
Clear Cookie
```

---

# Authentication Flow

## Login Flow

```text
Client
  ↓
POST /api/auth/login
  ↓
AuthController
  ↓
AuthService
  ↓
Validate Credentials
  ↓
Generate Access Token
  ↓
Generate Refresh Token
  ↓
Store Refresh Token Hash
  ↓
Return Access Token
  +
Set HttpOnly Refresh Cookie
```

---

## Refresh Flow

```text
Client
  ↓
POST /api/auth/refresh
  ↓
Read HttpOnly Cookie
  ↓
Hash Refresh Token
  ↓
Find Token in Database
  ↓
Validate Expiration
  ↓
Generate New Access Token
  ↓
Delete Old Refresh Token
  ↓
Create New Refresh Token
  ↓
Set New HttpOnly Cookie
```

---

## Logout Flow

```text
Client
  ↓
POST /api/auth/logout
  ↓
Validate Access Token
  ↓
Blacklist Access Token
  ↓
Delete Refresh Token from Database
  ↓
Clear Refresh Token Cookie
```

---

# Architecture

The project follows a layered architecture:

```text
Request
   ↓
Controller
   ↓
Service
   ↓
Repository
   ↓
Database
```

For API responses:

```text
Service
   ↓
Controller
   ↓
Resource
   ↓
ApiResponse
   ↓
JSON Response
```

### Current Authentication Components

```text
app/
├── Helpers/
│   └── ApiResponse.php
│
├── Http/
│   ├── Controllers/
│   │   └── Auth/
│   │       └── AuthController.php
│   │
│   ├── Requests/
│   │   └── Auth/
│   │       └── LoginRequest.php
│   │
│   └── Resources/
│       └── Auth/
│           └── LoginResource.php
│
├── Models/
│   ├── User.php
│   └── RefreshToken.php
│
├── Repositories/
│   ├── UserRepository.php
│   └── RefreshTokenRepository.php
│
└── Services/
    └── AuthService.php
```

---

# Security

### Passwords

Passwords are hashed using Laravel's password hashing mechanism.

### Access Tokens

JWT Access Tokens have a limited lifetime of:

```text
30 minutes
```

### Refresh Tokens

Refresh Tokens:

- Are generated using cryptographically secure random bytes.
- Are stored as SHA-256 hashes in the database.
- Are never returned in the JSON response.
- Are stored in an HttpOnly Cookie.
- Are rotated after successful refresh.
- Are deleted on logout.

### JWT Blacklisting

JWT blacklisting is enabled to prevent a logged-out Access Token from being reused.

---

# Implemented Endpoints

| Method | Endpoint | Auth | Status |
|---|---|---|---|
| POST | `/api/auth/login` | Public | Implemented |
| POST | `/api/auth/refresh` | Refresh Cookie | Implemented |
| POST | `/api/auth/logout` | Access Token | Implemented |

---

