# Finance App Backend - Secure API

The **Finance App Backend** is a robust and secure API built with Laravel, designed to power modern financial management applications. It features advanced authentication, security monitoring, and real-time validation.

[Features](#-features) • [Tech Stack](#-tech-stack) • [Getting Started](#-getting-started) • [Folder Structure](#-folder-structure) • [Main Project](../README.md)

## 🚀 Key Features

- **Advanced Authentication**: Secure login, registration, and logout using Laravel Sanctum.
- **Email Validation**: Real-time email existence checks and full MX record validation during registration.
- **Security Suite**:
    - OTP-based password reset flow.
    - Login history tracking.
    - Active session management (view and revoke sessions).
- **Profile Management**: Avatar uploads, profile updates, and secure email change requests.
- **Rate Limiting**: Intelligent throttling on sensitive endpoints to prevent brute-force attacks.

## 🛠️ Tech Stack

- **Framework**: [Laravel 12](https://laravel.com)
- **Documentation**: [Back to Main Project](../README.md)
- **Authentication**: [Laravel Sanctum](https://laravel.com/docs/sanctum)
- **Database**: MySQL
- **Server**: PHP 8.2+

## 📥 Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd finance-backend
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Configuration

Copy the `.env.example` to `.env` and configure your database and mail settings.

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup

```bash
php artisan migrate --seed
```

### 5. Start the Server

```bash
php artisan serve
```

The backend will be available at `http://localhost:8000`.

## 🌐 Frontend Integration

This backend is designed to work seamlessly with the Next.js frontend.

### Proxy Configuration

The frontend uses a proxy configuration in `next.config.mjs` to route requests to the backend:

```javascript
// finance-frontend/next.config.mjs
async rewrites() {
  return [
    { source: '/api/:path*', destination: 'http://localhost:8000/api/:path*' },
    { source: '/sanctum/csrf-cookie', destination: 'http://localhost:8000/sanctum/csrf-cookie' },
  ];
}
```

### Development Workflow

To run the full application:

1.  **Backend**: `php artisan serve` (runs on port 8000)
2.  **Frontend**: `npm run dev` (runs on port 3000)

## 📡 API Overview

| Endpoint               | Method | Description                  |
| :--------------------- | :----- | :--------------------------- |
| `/api/login`           | `POST` | User authentication          |
| `/api/register`        | `POST` | New user registration        |
| `/api/user`            | `GET`  | Get authenticated user data  |
| `/api/active-sessions` | `GET`  | View current active sessions |
| `/api/login-history`   | `GET`  | View recent login activity   |

---

_Note: Most routes are protected by the `auth:sanctum` middleware and require a valid session._
