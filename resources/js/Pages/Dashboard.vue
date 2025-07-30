<script setup>
import { defineProps } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';

const props = defineProps({
    employeeCount: Number,
})
</script>

<template>
    <Head title="Dashboard" />

    <MainLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                <Card>
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-semibold mb-2">Total Employees</h3>
                            <p class="text-3xl font-bold text-blue-600">{{ employeeCount ?? 0 }}</p>
                        </div>
                    </Card>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
