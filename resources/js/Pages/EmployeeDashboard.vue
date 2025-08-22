<template>
  <EmployeeMainLayout :employee-name="stats.employee_name">
    <h1 class="text-2xl font-bold mb-6">Employee Dashboard</h1>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-white shadow rounded-lg p-6 text-center">
        <div class="text-gray-500 text-sm mb-2">Total Days</div>
        <div class="text-2xl font-bold">{{ stats.total_days }}</div>
      </div>
      <div class="bg-green-100 shadow rounded-lg p-6 text-center">
        <div class="text-gray-500 text-sm mb-2">Present</div>
        <div class="text-2xl font-bold text-green-700">{{ stats.present }}</div>
      </div>
      <div class="bg-red-100 shadow rounded-lg p-6 text-center">
        <div class="text-gray-500 text-sm mb-2">Absent</div>
        <div class="text-2xl font-bold text-red-700">{{ stats.absent }}</div>
      </div>
      <div class="bg-blue-100 shadow rounded-lg p-6 text-center">
        <div class="text-gray-500 text-sm mb-2">Leaves</div>
        <div class="text-2xl font-bold text-blue-700">{{ stats.leaves }}</div>
      </div>
    </div>
  </EmployeeMainLayout>
</template>

<script setup>
import EmployeeMainLayout from '../Layouts/EmployeeMainLayout.vue';
defineProps({
  stats: Object
});
</script>
