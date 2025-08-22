## Frontend (Inertia + Vue 3)

### Entry & Setup
- `resources/js/app.js` initializes Inertia and Vue, registers FontAwesome icons, and Ziggy routes
- Pages are resolved dynamically from `resources/js/Pages/**/*.vue`

### Layouts & Components
- `resources/js/Layouts/` and `resources/js/Components/` hold reusable structure and UI elements (scan actual files to extend this section)

### Pages (by feature)
- Dashboard (Admin): `Pages/Dashboard.vue`
  - Receives counts: `employeeCount`, `presentCount`, `absentCount`, `lateCount`, `leaveCount`
- Employees:
  - List: `Pages/Employee.vue` (paginated, search query `?search=`)
  - Create: `Pages/CreateEmployee.vue`
  - Edit: `Pages/EditEmployee.vue`
  - Show: `Pages/ShowEmployee.vue`
- Attendance:
  - Index: `Pages/AttendanceShow.vue` (pagination and `date` filter)
  - Upload: `Pages/AttendanceUpload.vue` (CSV upload)
  - Note: `Pages/Attendance.vue` appears to be an alternate UI; confirm usage
- Payroll:
  - List & Generate: `Pages/Payroll.vue` (posts to `payroll.generate` and displays results)
- Leaves:
  - Admin moderation: `Pages/AdminLeave.vue`
  - Employee self-service: `Pages/EmployeeLeave.vue`
- Employee Dashboard: `Pages/EmployeeDashboard.vue`
- Auth & Profile:
  - Breeze-like auth pages under `Pages/Auth/`
  - Profile: `Pages/Profile/*` (Edit)
- Welcome: `Pages/Welcome.vue`

### Routing in Vue
- Ziggy exposes route names; use `route('name', params)` in components
- Inertia `Link` and `router` for navigation and form submissions

### Styling
- TailwindCSS with forms plugin and animate plugin

### Data Flow
- Controllers pass props to Inertia pages; pages render and submit back to named routes

### Notes
- Ensure consistency between backend route names and Ziggy config (`resources/js/ziggy.js`)