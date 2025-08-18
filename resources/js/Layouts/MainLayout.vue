  <template>
    <div>

      <header class="p-4 bg-gray-800 text-white flex items-center justify-between shadow">
        <h1 class="text-xl font-bold tracking-wide">Employee Management</h1>
        <div class="flex items-center gap-6">
          <div class="flex items-center gap-3 bg-gray-900 px-4 py-2 rounded-full shadow">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white text-lg font-bold border-2 border-white">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            </span>
            <span class="font-semibold text-base tracking-wide">admin</span>
          </div>
          <button @click="logout" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 font-semibold shadow">
            Logout
          </button>
        </div>
      </header>

      <div class="flex">
        

        <aside class="w-64 bg-gradient-to-b from-gray-900 to-gray-700 text-white min-h-screen shadow-lg">
          <nav class="h-full flex flex-col justify-between">
            <ul class="py-8 space-y-2">
              <li>
                <Link :href="route('admin.dashboard')" class="flex items-center px-5 py-3 rounded-lg hover:bg-gray-800 transition">
                  <svg class="w-5 h-5 mr-3 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6" /></svg>
                  <span>Dashboard</span>
                </Link>
              </li>
              <li>
                <Link :href="route('admin.employee.index')" class="flex items-center px-5 py-3 rounded-lg hover:bg-gray-800 transition">
                  <svg class="w-5 h-5 mr-3 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75M12 17v-1m0 0a4 4 0 00-4-4H8a4 4 0 00-4 4v1m8-1a4 4 0 014-4h.01a4 4 0 014 4v1" /></svg>
                  <span>Employees</span>
                </Link>
              </li>
              <li>
                <Link :href="route('attendance')" class="flex items-center px-5 py-3 rounded-lg hover:bg-gray-800 transition">
                  <svg class="w-5 h-5 mr-3 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                  <span>Attendance</span>
                </Link>
              </li>
              <li>
                <Link :href="route('attendance.show')" class="flex items-center px-5 py-3 rounded-lg hover:bg-gray-800 transition">
                  <svg class="w-5 h-5 mr-3 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 014-4h2a4 4 0 014 4v2" /></svg>
                  <span>Show Attendance</span>
                </Link>
              </li>
              <li>
                <Link :href="route('payroll')" class="flex items-center px-5 py-3 rounded-lg hover:bg-gray-800 transition">
                  <svg class="w-5 h-5 mr-3 text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 0V4m0 8v8" /></svg>
                  <span>Generate Payroll</span>
                </Link>
              </li>
              <li>
                <Link :href="route('admin.leaves')" class="flex items-center px-5 py-3 rounded-lg hover:bg-gray-800 transition">
                  <svg class="w-5 h-5 mr-3 text-pink-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17l4-4 4 4m0 0V3m0 14H4" /></svg>
                  <span>Leaves</span>
                </Link>
              </li>
              <li>
                <Link :href="route('attendance.upload.form')" class="flex items-center px-5 py-3 rounded-lg hover:bg-gray-800 transition">
                  <svg class="w-5 h-5 mr-3 text-teal-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v16c0 1.1.9 2 2 2h12a2 2 0 002-2V4" /></svg>
                  <span>Upload Attendance</span>
                </Link>
              </li>
            </ul>
            <div class="px-5 pb-8">
              <div class="text-xs text-gray-400">&copy; {{ new Date().getFullYear() }} EMS Admin</div>
            </div>
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
