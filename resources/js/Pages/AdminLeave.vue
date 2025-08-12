<template>
  <MainLayout>
  <div class="p-6">
    <h1 class="text-xl font-bold mb-4">Pending Leave Requests</h1>
    <table class="w-full border">
      <thead>
        <tr>
          <th class="border p-2">Employee</th>
          <th class="border p-2">Date</th>
          <th class="border p-2">Type</th>
          <th class="border p-2">Reason</th>
          <th class="border p-2">Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="leave in leaves" :key="leave.id">
          <td class="border p-2">{{ leave.employee.name }}</td>
          <td class="border p-2">{{ leave.date }}</td>
          <td class="border p-2">{{ leave.leave_type }}</td>
          <td class="border p-2">{{ leave.reason }}</td>
          <td class="border p-2">
            <button @click="approveLeave(leave.id)" class="bg-green-500 text-white p-1 rounded mr-2">Approve</button>
            <button @click="rejectLeave(leave.id)" class="bg-red-500 text-white p-1 rounded">Reject</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  leaves: Array
});

const approveLeave = (id) => {
  router.post(route('leaves.approve', id));
};

const rejectLeave = (id) => {
  router.post(route('leaves.reject', id));
};
</script>
