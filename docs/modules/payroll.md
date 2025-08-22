## Payroll Module

### Purpose
Compute payroll metrics (worked minutes, overtime, lates, leaves, absences, deductions) per employee for a given date range and persist monthly summaries.

### Key Files
- Controller: `app/Http/Controllers/PayrollController.php`
- Model: `app/Models/Payroll.php`
- Page: `resources/js/Pages/Payroll.vue`
- Routes: Admin group in `routes/web.php`

### Data Model
- Table: `payrolls` (see database-schema)
- Relationships: `payroll->employee()` belongsTo

### Behaviors
- Index: returns list of payrolls with employee
- Generate (`POST /payroll/generate`):
  - Validates `start_date` and `end_date` (Y-m-d)
  - For each employee:
    - Loads attendances within range
    - Sums worked minutes where `time_out > time_in`
    - Calculates overtime over 8 hours/day
    - Computes working days excluding weekends
    - Calculates `hourly_rate = salary / (workingDays*8)` if possible
    - `total_salary = (totalMinutes/60) * hourly_rate`
    - Counts approved leaves within range, lates (time_in > 09:15), absences = workingDays - attendanceCount - approvedLeaves
    - Deductions: absences, unpaid leaves, rejected leaves as full-day; late penalty is half-hourly-rate per late
    - Upserts a `payroll` for `(employee_id, month)` where `month` is first day of `start_date` month
  - Returns JSON with payrolls for that month

### Notes
- `total_deduction_amount` stored as integer; consider decimal precision
- Ensure `leaves` have `leave_type` values like `Unpaid` when applying unpaid leave logic