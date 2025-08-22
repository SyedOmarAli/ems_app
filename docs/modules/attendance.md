## Attendance Module

### Purpose
Track daily attendance, allow CSV uploads, manual updates, and listing with filters.

### Key Files
- Controller: `app/Http/Controllers/AttendanceController.php`
- Model: `app/Models/Attendance.php`
- Pages: `resources/js/Pages/AttendanceShow.vue`, `AttendanceUpload.vue`, `Attendance.vue`
- Routes: Admin group in `routes/web.php`

### Data Model
- Table: `attendances` (date, time_in, time_out, status)
- Relationships: `attendance->employee()` belongsTo

### Behaviors
- Index: paginated list with optional `date` filter
- Show: full list ordered by date desc
- Update Status: accepts either single record or `records[]` array; upserts by `(employee_id,date)`
- Upload CSV:
  - Validates CSV
  - Parses flexible datetime formats (12/24h, AM/PM spacing)
  - Skips weekends
  - For first scan before noon: sets `time_in` and status `Present` or `Late` (after 09:15)
  - For afternoon-only scans: sets `time_out` with status `Absent`
  - For second scans on same day: updates `time_out` if later
  - After processing, creates `Absent` entries for all employees without records for processed dates (skipping weekends)

### Notes
- Consider using casts for times on the model for consistency