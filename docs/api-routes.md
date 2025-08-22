## API and Web Routes

All routes are defined in `routes/web.php` and `routes/auth.php` and return Inertia pages or JSON where noted.

### Public
- `GET /` → Inertia `Welcome`

### Auth (guest)
- `GET /register` → show registration
- `POST /register` → create user
- `GET /login` → show login
- `POST /login` → authenticate
- `GET /forgot-password`, `POST /forgot-password`
- `GET /reset-password/{token}`, `POST /reset-password`

### Authenticated (shared)
- `GET /profile` (`profile.edit`)
- `PATCH /profile` (`profile.update`)
- `DELETE /profile` (`profile.destroy`)

### Admin (middleware: `auth`, `role:admin`, `verified`)
- Dashboard
  - `GET /admin/dashboard` (`admin.dashboard`) → Admin overview metrics
- Employees
  - `GET /admin/employee` (`admin.employee.index`) → List (paginated, searchable)
  - `GET /admin/employee/create` (`admin.employee.create`) → Create form
  - `POST /admin/employee` (`admin.employee.store`) → Create employee + linked user
  - `GET /admin/employee/{employee}/edit` (`admin.employee.edit`) → Edit form
  - `PUT /admin/employee/{employee}` (`admin.employee.update`) → Update
  - `DELETE /admin/employee/{employee}` (`admin.employee.destroy`) → Delete (also deletes linked user)
  - `GET /admin/employee/{employee}` (`admin.employee.show`) → Detail
- Attendance
  - `GET /attendance` (`attendance`) → Paginated index (filter by `date`)
  - `GET /attendance/upload-form` (`attendance.upload.form`) → Upload UI
  - `POST /attendance/update-status` (`attendance.update_status`) → Bulk/single update
  - `GET /attendance/show` (`attendance.show`) → Full list
  - `POST /attendance/upload` (`attendance.upload`) → CSV upload and process
  - `GET /attendance/sample-csv` (`attendance.sample.csv`) → Sample download (route exists; controller method not present)
- Payroll
  - `GET /payroll` (`payroll`) → List
  - `POST /payroll/generate` (`payroll.generate`) → Generate for date range; returns JSON
- Leaves (admin moderation)
  - `GET /admin/leaves` (`admin.leaves`) → All leaves
  - `POST /admin/leaves/{leave}/approve` (`admin.leaves.approve`)
  - `POST /admin/leaves/{leave}/reject` (`admin.leaves.reject`)
  - `POST /admin/leaves/{leave}/revert` (`admin.leaves.revert`)

### Employee (middleware: `auth`, `role:employee`, `verified`)
- `GET /employee/dashboard` (`employee.dashboard`)
- `GET /employee/leaves` (`employee.leaves`)
- `POST /employee/leaves/apply` (`employee.leaves.apply`)

Notes:
- Frontend uses Ziggy to reference these routes by name inside Vue components.