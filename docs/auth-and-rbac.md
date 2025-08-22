## Authentication & RBAC

### Authentication
- Session-based auth with Laravel; routes in `routes/auth.php` provide:
  - `GET /login`, `POST /login`
  - `GET /register`, `POST /register`
  - Password reset: `GET /forgot-password`, `POST /forgot-password`, `GET /reset-password/{token}`, `POST /reset-password`
  - Email verification: `GET /verify-email`, `GET /verify-email/{id}/{hash}`, `POST /email/verification-notification`
  - `POST /logout`
- Profile management: `GET /profile`, `PATCH /profile`, `DELETE /profile`

### Roles & Permissions
- Package: `spatie/laravel-permission`
- Seeded roles: `admin`, `employee`
- Default seeded user: `admin@gmail.com` / `password` assigned role `admin` (see `database/seeders/DatabaseSeeder.php`)

### Middleware
- `auth`: ensures authenticated session
- `verified`: enforces email verification
- `role:<role>`: custom `App\Http\Middleware\RoleMiddleware` wrapper over Spatie roles
  - Used in route groups to protect Admin and Employee areas

### Route Groups
- Admin area: `middleware(['auth','role:admin','verified'])`
- Employee area: `middleware(['auth','role:employee','verified'])`

### User and Employee Linkage
- `users` has-one `employees`; `employees.user_id` FK
- On employee creation, a `User` is created with default password and role `employee`, then linked via `user_id`

### Notes
- Sanctum is required for token-based auth but is not used in current routes