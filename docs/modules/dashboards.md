## Dashboards

### Admin Dashboard
- Controller: `app/Http/Controllers/DashboardController.php`
- Page: `resources/js/Pages/Dashboard.vue`
- Metrics (for today):
  - `employeeCount`
  - `presentCount`
  - `absentCount`
  - `lateCount`
  - `leaveCount`

### Employee Dashboard
- Controller: `app/Http/Controllers/EmployeeDashboardController.php`
- Page: `resources/js/Pages/EmployeeDashboard.vue`
- Computes stats for current month:
  - `total_days` (attendance records)
  - `present`, `absent`
  - `leaves` (note: code references `whereBetween('date', ...)` though `leaves` use `from_date`/`to_date`)
  - `employee_name`

### Notes
- Consider aligning leaves date fields used in employee dashboard with the final schema