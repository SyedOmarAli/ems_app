<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';
const props = defineProps({
    employee: Object
});
</script>

<template>
    <Head title="Employee Details" />
    <MainLayout>
        <div class="p-6 max-w-xl mx-auto bg-white shadow rounded">
            <h2 class="text-2xl font-bold mb-4">Employee Details</h2>
            <p><strong>Name:</strong> {{ employee.name }}</p>
            <p><strong>Email:</strong> {{ employee.email }}</p>
            <p><strong>Phone:</strong> {{ employee.phone }}</p>
            <p><strong>Salary:</strong> {{ employee.salary }}</p>
            <p><strong>Hire Date:</strong> {{ employee.hire_date }}</p>
            <p><strong>Department:</strong> {{ employee.department }}</p>
        </div>
    </MainLayout>
</template>