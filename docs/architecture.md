## Architecture Overview

- Backend: Laravel 12 (PHP ^8.2)
  - Key packages: `inertiajs/inertia-laravel`, `laravel/sanctum`, `spatie/laravel-permission`, `league/csv`, `tightenco/ziggy`
- Frontend: Vue 3 + Inertia + Vite + TailwindCSS
  - Key libs: `@inertiajs/vue3`, `axios`, `ziggy-js`, `@fortawesome/*`
- Auth: Laravel session auth with email verification; role-based access via Spatie Permissions and custom `role` middleware
- DB: MySQL/PostgreSQL/SQLite (via Eloquent + migrations)

### Request Flow
1. User hits route in `routes/web.php`
2. Middleware stack applies: `auth`, `role:<role>`, `verified`, Inertia handler
3. Controller performs domain logic and queries Eloquent models
4. Response rendered to an Inertia page component under `resources/js/Pages`
5. Vue component fetches/receives props, renders UI; interactions call named routes (Ziggy)

### Key Directories
- `app/Http/Controllers`: Feature controllers
- `app/Models`: Eloquent models and relationships
- `app/Http/Middleware`: Request middleware, including `RoleMiddleware`
- `database/migrations`: Database schema definition
- `resources/js`: SPA entry (`app.js`), Pages, Layouts, Components
- `routes/web.php` and `routes/auth.php`: Route definitions

### Environments & Dependencies
- PHP ^8.2, Node 18+, npm 9+, a SQL database
- Composer dev script: serves Laravel + Vite concurrently

### Setup & Run
1. Copy env and install dependencies
   - `cp .env.example .env`
   - `composer install`
   - `npm install`
2. App key and database setup
   - Set DB credentials in `.env`
   - `php artisan key:generate`
   - `php artisan migrate --seed`
     - Seeds roles `admin`/`employee` and creates admin user `admin@gmail.com` / `password`
3. Start the app
   - Separate: `php artisan serve` and `npm run dev`
   - Or: `composer run dev` (runs both via `concurrently`)

### Build
- Production build: `npm run build` then serve public assets with Laravel

### Error Handling & Logging
- Controllers wrap risky operations in try/catch and log errors via `\Log`
- Validation via FormRequest or `$request->validate()`

### Notable Behaviors
- Inertia/Vue provides SPA experience without a separate API layer
- RBAC enforced via route groups using `role:admin` and `role:employee`
- `Ziggy` exposes named Laravel routes to Vue