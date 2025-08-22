## Employee Module

### Purpose
Manage employee records: CRUD, linkage to `users`, and role assignment.

### Key Files
- Controller: `app/Http/Controllers/EmployeeController.php`
- Model: `app/Models/Employee.php`
- Views/Pages: `resources/js/Pages/Employee.vue`, `CreateEmployee.vue`, `EditEmployee.vue`, `ShowEmployee.vue`
- Routes: under Admin group in `routes/web.php`

### Data Model
- Table: `employees` (see database-schema)
- Relationships:
  - `employee->user()` belongsTo `users`
  - `employee->attendance()` hasMany `attendances`
  - `employee->leaves()` hasMany `leaves`

### Behaviors
- List with search by `name`, `email`, `department`
- Create employee:
  - Validates fields
  - Creates a `User` with default password `pass123`
  - Assigns role `employee`
  - Creates `Employee` linked via `user_id`
- Update employee:
  - Validates and updates both `employees` and linked `users` name/email
- Delete employee:
  - Deletes linked `users` record (cascade)
- Show employee: detail view

### Validation
- `name`, `email` (unique across `employees` and `users`), `phone`, `salary` (<= 100), `hire_date`, `code` (required on create), `department`

### Notes
- Consider enforcing better password handling and invite flows
- `salary` max 100 may be a placeholder; adjust to business rules