## Database Schema

### `users`
- `id` bigint PK
- `name` string
- `email` string unique
- `password` string (hashed)
- `email_verified_at` datetime nullable
- Timestamps
- Relationships: `hasOne` `employees`

### `employees`
- `id` bigint PK
- `user_id` bigint FK -> `users.id` (nullable, cascade on delete)
- `code` string unique
- `name` string
- `email` string unique
- `phone` string
- `salary` integer
- `hourly_rate` decimal(8,2) default 200
- `hire_date` date
- `department` string nullable
- `designation` string nullable
- `role` enum('admin','employee') default 'employee'
- Timestamps

### `attendances`
- `id` bigint PK
- `employee_id` bigint FK -> `employees.id` (cascade on delete)
- `date` date
- `time_in` time nullable
- `time_out` time nullable
- `status` enum('Present','Absent','Late','Leave') default 'Absent'
- Timestamps

### `payrolls`
- `id` bigint PK
- `employee_id` bigint FK -> `employees.id` (cascade on delete)
- `start_date` date nullable
- `end_date` date nullable
- `month` date NOT NULL
- `total_minutes` integer default 0
- `overtime` integer
- `hourly_rate` decimal(10,2) default 0
- `total_salary` decimal(10,2) default 0
- `total_lates` integer default 0
- `total_absents` integer default 0
- `total_deduction_amount` integer default 0
- `total_leaves` integer default 0
- Timestamps

### `leaves`
- `id` bigint PK
- `employee_id` bigint FK -> `employees.id` (cascade on delete)
- `from_date` date nullable
- `to_date` date nullable
- `leave_type` string(20) nullable
- `reason` string nullable
- `status` string(20) default 'Pending'
- `total_leaves` integer default 0
- Timestamps

### Spatie Permission Tables
- Standard `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions` per package

Notes:
- Some earlier migrations add/drop `leaves.date`. The final schema uses `from_date`/`to_date`.
- `payrolls.total_deduction_amount` is an integer whereas controller computes a decimal; adjust if needed.