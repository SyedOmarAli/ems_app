<template>
    <div>
        <header class="p-4 bg-gray-800 text-white flex items-center justify-between shadow">
            <div class="flex items-center gap-4">
                <h1 class="text-xl font-bold tracking-wide">Employee Management</h1>
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full bg-blue-600 text-white text-sm font-semibold shadow">
                    <svg class="w-5 h-5 mr-1 text-white" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ employeeName }}
                </span>
            </div>
            <button @click="logout"
                class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 font-semibold shadow">
                Logout
            </button>
        </header>
        <div class="flex">
            <aside class="w-64 bg-gray-100 p-4 min-h-screen">
                <nav>
                    <ul>
                        <li>
                            <Link :href="route('employee.dashboard')"
                                class="block py-2 px-4 rounded hover:bg-blue-100 font-medium text-blue-700">Dashboard
                            </Link>
                            <Link :href="route('employee.leaves')"
                                class="block py-2 px-4 rounded hover:bg-blue-100 font-medium text-blue-700">Apply for
                            leaves</Link>
                            <Link :href="route('profile.edit')"
                                class="block py-2 px-4 rounded hover:bg-blue-100 font-medium text-blue-700">Change Password</Link>
                        </li>
                    </ul>
                </nav>
            </aside>
            <main class="flex-1 p-6 bg-gray-50 min-h-screen">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
const props = defineProps({
    employeeName: String
});

function logout() {
    router.post(route('logout'));
}
</script>
