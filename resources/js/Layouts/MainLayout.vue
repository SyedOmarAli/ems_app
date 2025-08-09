  <template>
    <div>
      <header class="p-4 bg-gray-800 text-white flex justify-between">
        <h1 class="text-xl font-bold">Employee Management</h1>
        <button @click="logout"  class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
          >Logout</button>
      </header>

      <div class="flex">
        
        <aside class="w-64 bg-gray-100 p-4">
          <nav>
            <ul>
              <li><Link :href="route('admin.dashboard')">Dashboard</Link></li>
              <li><Link :href="route('employee.index')">Employees</Link></li>
              <li><Link :href="route('attendance')">Attendance</Link></li>
              <li><Link :href="route('attendance.show')">Show Attendance</Link></li>
              <li><Link :href="route('payroll')">Generate Payroll</Link></li>
              <li><Link :href="route('admin.leaves')">Leaves</Link></li>
              <li>
              <Link :href="route('attendance.upload.form')">Upload Attendance</Link>
             </li>
              <!-- Add more menu items -->
            </ul>
          </nav>
        </aside>

        <main class="flex-1 p-6">
          <slot />
        </main>
      </div>
    </div>
  </template>

  <script setup>
  import { Link } from '@inertiajs/vue3' 
  import { router } from '@inertiajs/vue3';
  

  function logout() {
    router.post(route('logout'));
  }
  </script>
