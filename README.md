<div align="center">

# Gym Management System

### Multi-Tenant Gym Management REST API

A Laravel-powered backend for managing gym operations, memberships, attendance, staff, inventory, and financial reporting.

**PHP · Laravel · MySQL · JWT · REST API**

[Features](#features) · [Architecture](#architecture) · [Getting Started](#getting-started) · [API Reference](#api-reference) · [Deployment](#deployment)

</div>

---

## Overview

**Gym Management System (GMS)** is a multi-tenant backend application designed to centralize day-to-day gym operations. It provides a role-based REST API for managing members, membership plans, subscriptions, attendance, employees, equipment, expenses, store transactions, and administrative reminders.

Each request is associated with the authenticated user's gym, keeping operational data separated between gyms.

## Features

| Module | Capabilities |
| --- | --- |
| **Authentication** | Login with email or phone, JWT access tokens, rotating refresh tokens via HttpOnly cookies, and logout |
| **Members** | Member registration, search, profile updates, listing, and deletion |
| **Membership Plans** | Limited-session and unlimited plans, configurable duration, pricing, and inactivity thresholds |
| **Subscriptions** | Subscription creation and renewal, payment tracking, and membership validity rules |
| **Attendance** | Check-in, check-out, daily attendance, currently-inside tracking, and session-limit enforcement |
| **Offline Synchronization** | Batch synchronization of offline check-ins with UUID-based duplicate detection and per-record results |
| **Member Engagement** | New, inactive, expiring, and expired member segments; customizable message templates and WhatsApp click-to-chat links |
| **Dashboard** | Active memberships, today's attendance, segmentation counts, and a seven-day attendance trend |
| **Staff & Shifts** | Staff records, shift management, and staff attendance |
| **Equipment** | Gym equipment listing, creation, and updates |
| **Expenses** | Expense tracking, filtering, and monthly summaries |
| **Store** | Product management, sales recording, and stock updates |
| **Financial Reports** | Subscription revenue, store revenue, expenses, and net profit for a selected date range |
| **Administrative Reminders** | Create reminders and filter upcoming or due tasks |

## Technology Stack

| Layer | Technology |
| --- | --- |
| Backend | PHP, Laravel |
| Database | MySQL |
| Authentication | JWT access tokens and refresh tokens |
| API | RESTful JSON endpoints |
| Architecture | Controllers, Form Requests, Services, Repositories, API Resources |
| Deployment | Railway |

## Architecture

The application separates HTTP handling, business logic, data access, and response formatting.

```text
HTTP Request
    |
    v
Routes & Middleware
    |
    v
Controller / Form Request
    |
    v
Service (Business Logic)
    |
    v
Repository (Data Access)
    |
    v
Eloquent Models / MySQL
    |
    v
API Resource / JSON Response
```

Core architectural practices include:

- **Multi-tenancy:** Gym-specific records are scoped using the authenticated user's `gym_id`.
- **Role-based authorization:** Protected actions are restricted according to user roles.
- **Input validation:** Form Requests and validation rules protect API operations.
- **Consistent responses:** Endpoints return structured JSON success and error messages.
- **Data integrity:** Transactions and locking are used in critical attendance and sales workflows.
- **Timezone awareness:** Gym-facing date calculations use `Africa/Cairo` where applicable.

## Roles & Permissions

The system supports three roles: **Owner**, **Receptionist**, and **Trainer**.

| Operation | Owner | Receptionist | Trainer |
| --- | :---: | :---: | :---: |
| View dashboard statistics | ✓ | ✓ | — |
| View member segmentation | ✓ | ✓ | — |
| View staff and shifts | ✓ | ✓ | — |
| Create staff and shifts | ✓ | — | — |
| Manage membership plans | ✓ | — | — |
| View and manage store products | ✓ | ✓ | — |
| Delete store products | ✓ | — | — |
| Record store sales | ✓ | ✓ | — |
| View store sales | ✓ | — | — |
| View financial reports | ✓ | — | — |
| Manage administrative reminders | ✓ | — | — |
| Update message templates | ✓ | — | — |

Other authenticated endpoints follow their own route and middleware rules.

## Getting Started

### Prerequisites

- PHP and Composer compatible with the project's `composer.json`
- MySQL
- Required PHP extensions for Laravel

### Installation

```bash
git clone https://github.com/devnova-team/gym-management-system.git
cd gym-management-system
composer install
cp .env.example .env
php artisan key:generate
```

Configure the database connection in `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gym_management_system
DB_USERNAME=root
DB_PASSWORD=
```

Create the database, then run:

```bash
php artisan migrate
php artisan db:seed
php artisan db:seed --class=MessageTemplateSeeder
php artisan serve
```



## API Reference

**Base path:** `/api`  
**Production API:** [gym-management-system-production-6e07.up.railway.app](https://gym-management-system-production-6e07.up.railway.app)

Most protected endpoints require:

```http
Authorization: Bearer <access_token>
Accept: application/json
```

### 1. Authentication

JWT-based authentication with email or phone login, rotating refresh tokens, and logout.

| Method | Endpoint | Description |
| --- | --- | --- |
| `POST` | `/auth/login` | Sign in with email or phone |
| `POST` | `/auth/refresh` | Obtain a new access token using the HttpOnly refresh-token cookie |
| `POST` | `/auth/logout` | Invalidate the current session |

**Login example**

```http
POST /api/auth/login
Content-Type: application/json
Accept: application/json
```

```json
{
  "email": "user@example.com",
  "password": "your_password"
}
```

A successful login returns a JWT access token, token type, expiration time, gym ID, and user role.

### 2. Member Management

Register members, search by name or phone number, and maintain gym-specific member records.

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/members` | List members; supports `?search=` |
| `POST` | `/members` | Register a member |
| `GET` | `/members/{id}` | Retrieve member details |
| `PUT` | `/members/{id}` | Update a member |
| `DELETE` | `/members/{id}` | Delete a member |

### 3. Membership Plans

Manage configurable limited-session and unlimited membership plans.

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/plans` | List plans |
| `GET` | `/plans/{id}` | Retrieve plan details |
| `POST` | `/plans` | Create a plan (Owner) |
| `PUT` | `/plans/{id}` | Update a plan (Owner) |
| `DELETE` | `/plans/{id}` | Delete a plan (Owner) |

Limited plans specify `total_sessions`; unlimited plans do not impose a session-count limit.

### 4. Subscriptions

Create and renew paid memberships while preserving subscription history.

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/subscriptions` | List subscriptions |
| `GET` | `/subscriptions/{id}` | Retrieve a subscription |
| `POST` | `/subscriptions` | Create a subscription |
| `POST` | `/subscriptions/renew` | Renew a subscription |

### 5. Member Attendance

Track member visits and enforce subscription validity and session limits.

| Method | Endpoint | Description |
| --- | --- | --- |
| `POST` | `/attendance/check-in` | Check in a member |
| `POST` | `/attendance/{id}/check-out` | Check out an attendance record |
| `GET` | `/attendance/today` | List today's attendance |
| `GET` | `/attendance/currently-inside` | List members currently checked in |

### 6. Offline Attendance Synchronization

Synchronize offline check-ins with UUID-based deduplication and per-record results.

| Method | Endpoint | Description |
| --- | --- | --- |
| `POST` | `/attendance/sync` | Synchronize a batch of offline check-ins |

### 7. Member Segmentation

Identify new, inactive, expiring, and expired members based on membership and attendance history.

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/members/segmented` | Retrieve segmented members (Owner, Receptionist) |

Segmentation responses can include personalized WhatsApp click-to-chat links; messages are not sent automatically.

### 8. Message Templates

Manage reusable, gym-specific messages for member engagement.

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/message-templates` | List message templates |
| `PUT` | `/message-templates` | Update a template (Owner) |

### 9. Dashboard Statistics

Provide operational insights including active memberships, today's attendance, member segments, and a seven-day attendance trend.

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/dashboard/stats` | Retrieve dashboard statistics (Owner, Receptionist) |

### 10. Staff Management

Manage gym staff records and staff attendance.

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/staff` | List staff (Owner, Receptionist) |
| `POST` | `/staff` | Create staff (Owner) |
| `POST` | `/staff/attendance` | Record staff attendance (Owner) |

### 11. Shift Management

Create and retrieve staff shifts.

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/shifts` | List shifts (Owner, Receptionist) |
| `POST` | `/shifts` | Create a shift (Owner) |

### 12. Equipment Management

Track gym equipment and update equipment records.

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/equipment` | List equipment (Owner) |
| `POST` | `/equipment` | Add equipment (Owner) |
| `PUT` | `/equipment/{id}` | Update equipment (Owner) |

### 13. Expense Management

Record operational expenses and view monthly summaries.

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/expenses` | List and filter expenses |
| `POST` | `/expenses` | Record an expense |
| `GET` | `/expenses/monthly-summary` | Retrieve a monthly expense summary |

### 14. Store Product Management

Maintain products and inventory within each gym's store.

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/store/products` | List products (Owner, Receptionist) |
| `GET` | `/store/products/{id}` | Retrieve product details (Owner, Receptionist) |
| `POST` | `/store/products` | Add a product (Owner, Receptionist) |
| `PUT` | `/store/products/{id}` | Update a product (Owner, Receptionist) |
| `DELETE` | `/store/products/{id}` | Delete a product (Owner) |

### 15. Store Sales

Record store transactions and maintain inventory consistency.

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/store/sales` | List sales (Owner) |
| `POST` | `/store/sales` | Record a sale (Owner, Receptionist) |

### 16. Financial Summary

Calculate subscription revenue, store revenue, expenses, and net profit for a selected date range.

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/reports/financial-summary` | Retrieve a date-range financial summary (Owner); accepts `from` and `to` |

**Example request**

```http
GET /api/reports/financial-summary?from=2026-10-01&to=2026-10-31
Authorization: Bearer <access_token>
```

**Example response**

```json
{
  "success": true,
  "message": "Financial summary retrieved successfully.",
  "data": {
    "from": "2026-10-01",
    "to": "2026-10-31",
    "subscription_revenue": "900.00",
    "store_revenue": "200.00",
    "total_revenue": "1100.00",
    "total_expenses": "150.00",
    "net_profit": "950.00"
  }
}
```

### 17. Administrative Reminders

Create gym-specific reminders and filter them by due status.

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/reminders` | List reminders; supports `?status=upcoming` or `?status=due` (Owner) |
| `POST` | `/reminders` | Create a reminder (Owner) |

**Example request**

```http
POST /api/reminders
Authorization: Bearer <access_token>
Content-Type: application/json
```

```json
{
  "title": "Check treadmill maintenance",
  "due_date": "2026-11-05",
  "reminder_note": "Contact the maintenance technician."
}
```

### Response Conventions

Most endpoints use a response structure similar to:

```json
{
  "success": true,
  "message": "Operation completed successfully.",
  "data": {}
}
```

Common error statuses:

| Status | Meaning |
| --- | --- |
| `401` | Missing, expired, or invalid authentication |
| `403` | Insufficient permissions |
| `404` | Resource not found |
| `422` | Validation or business-rule failure |
| `429` | Rate limit exceeded |

## Key Business Rules

- **Subscriptions:** Paid subscriptions determine membership eligibility; renewals create new subscription records rather than overwriting historical ones.
- **Plans:** Limited plans enforce a total session allowance; unlimited plans have no session-count restriction within their validity period.
- **Attendance:** A member cannot check in twice on the same Cairo calendar day or while a previous check-in remains open.
- **Offline check-ins:** Each offline record has a unique `local_id`; repeated matching submissions are recognized without creating duplicate attendance records.
- **Engagement:** Member segmentation uses subscription validity and attendance history. WhatsApp links prefill messages but do not automatically send them.
- **Financial reporting:** Revenue combines recorded subscription payments and store sales; net profit subtracts expenses from total revenue.
- **Reminders:** A reminder is `upcoming` when its due date is in the future and `due` when its due date is today or earlier.

## Deployment

The API is deployed on **Railway** with a MySQL database.

```bash
railway status
railway up
```

Database schema updates use Laravel migrations:

```bash
php artisan migrate --force
```


## Project Structure

```text
app/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Resources/
├── Models/
├── Repositories/
└── Services/
database/
├── migrations/
└── seeders/
routes/
└── api.php
```

---

<div align="center">

# Gym Management System

**Laravel REST API · Multi-Tenant Architecture**

<sub>Documentation by <a href="https://github.com/N0URHAN01">Nourhan Mohammed Shaban</a></sub>

</div>
