<template>
  <div class="p-6">
    <h1 class="text-xl font-bold mb-4">My Leaves</h1>
    <form @submit.prevent="applyLeave" class="space-y-4 mb-6">
      <input v-model="form.date" type="date" class="border p-2 rounded w-full" />
      <select v-model="form.leave_type" class="border p-2 rounded w-full">
        <option value="paid">Paid Leave</option>
        <option value="unpaid">Unpaid Leave</option>
        <option value="sick">Sick Leave</option>
      </select>
      <textarea v-model="form.reason" placeholder="Reason" class="border p-2 rounded w-full"></textarea>
      <button class="bg-blue-600 text-white p-2 rounded">Apply</button>
    </form>

    <h2 class="text-lg font-semibold mb-2">Leave History</h2>
    <table class="w-full border">
      <thead>
        <tr>
          <th class="border p-2">Date</th>
          <th class="border p-2">Type</th>
          <th class="border p-2">Status</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="leave in leaves" :key="leave.id">
          <td class="border p-2">{{ leave.date }}</td>
          <td class="border p-2">{{ leave.leave_type }}</td>
          <td class="border p-2">{{ leave.status }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  leaves: Array
});

const form = useForm({
  date: '',
  leave_type: '',
  reason: ''
});

const applyLeave = () => {
  form.post(route('leaves.store'));
};
</script>
