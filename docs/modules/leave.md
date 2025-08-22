## Leave Module

### Purpose
Allow employees to apply for leave and admins to approve/reject/revert requests.

### Key Files
- Controller: `app/Http/Controllers/LeaveController.php`
- Model: `app/Models/Leaves.php`
- Pages: `resources/js/Pages/EmployeeLeave.vue`, `AdminLeave.vue`
- Routes: Admin and Employee groups in `routes/web.php`

### Data Model
- Table: `leaves` (from_date, to_date, leave_type, reason, status)
- Relationship: `leave->employee()` belongsTo

### Behaviors
- Employee:
  - `GET /employee/leaves`: shows own leaves with name in stats
  - `POST /employee/leaves/apply`: validates and creates leave with `Pending` status
- Admin:
  - `GET /admin/leaves`: lists all leaves
  - `POST /admin/leaves/{leave}/approve|reject|revert`: updates `status`

### Validation (apply)
- `from_date` required, date
- `to_date` required, date, after_or_equal:from_date
- `leave_type` required
- `reason` optional

### Notes
- Employee is resolved via `employees.user_id = auth()->id()`
- Extend with quotas, attachments, and overlapping-date checks as needed