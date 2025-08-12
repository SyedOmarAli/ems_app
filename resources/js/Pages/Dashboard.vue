
<script setup>
import { defineProps } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    employeeCount: Number,
    presentCount: Number,
    absentCount: Number,
    lateCount: Number,
    leaveCount: Number,
    payrollCount: Number,
});
</script>

<template>
    <Head title="Admin Dashboard" />
    <MainLayout>
        <div class="py-10 px-4 md:px-8 bg-gray-50 min-h-screen">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Admin Dashboard</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
                <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
                    <div class="bg-blue-100 text-blue-700 rounded-full p-3 mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75M12 17v-1m0 0a4 4 0 00-4-4H8a4 4 0 00-4 4v1m8-1a4 4 0 014-4h.01a4 4 0 014 4v1" /></svg>
                    </div>
                    <div class="text-gray-500 text-sm mb-1">Total Employees</div>
                    <div class="text-3xl font-bold text-blue-700">{{ employeeCount ?? 0 }}</div>
                </div>
                <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
                    <div class="bg-green-100 text-green-700 rounded-full p-3 mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 014-4h2a4 4 0 014 4v2" /></svg>
                    </div>
                    <div class="text-gray-500 text-sm mb-1">Total Present</div>
                    <div class="text-3xl font-bold text-green-700">{{ presentCount ?? 0 }}</div>
                </div>
                <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
                    <div class="bg-green-100 text-green-700 rounded-full p-3 mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 014-4h2a4 4 0 014 4v2" /></svg>
                    </div>
                    <div class="text-gray-500 text-sm mb-1">Total Absent</div>
                    <div class="text-3xl font-bold text-green-700">{{ absentCount ?? 0 }}</div>
                </div>
                <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
                    <div class="bg-green-100 text-green-700 rounded-full p-3 mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 014-4h2a4 4 0 014 4v2" /></svg>
                    </div>
                    <div class="text-gray-500 text-sm mb-1">Total Late</div>
                    <div class="text-3xl font-bold text-green-700">{{ lateCount ?? 0 }}</div>
                </div>
                <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
                    <div class="bg-green-100 text-green-700 rounded-full p-3 mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 014-4h2a4 4 0 014 4v2" /></svg>
                    </div>
                    <div class="text-gray-500 text-sm mb-1">On Leave</div>
                    <div class="text-3xl font-bold text-green-700">{{ leaveCount ?? 0 }}</div>
                </div>
                <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
                    <div class="bg-yellow-100 text-yellow-700 rounded-full p-3 mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17l4-4 4 4m0 0V3m0 14H4" /></svg>
                    </div>
                    <div class="text-gray-500 text-sm mb-1">Leave Requests</div>
                    <div class="text-3xl font-bold text-yellow-700">{{ leaveCount ?? 0 }}</div>
                </div>
                
            </div>
            <!-- Add more admin widgets or charts here -->
        </div>
    </MainLayout>
</template>
