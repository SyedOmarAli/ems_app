
<template>
  <EmployeeMainLayout :employee-name="stats.employee_name">
    <div class="max-w-3xl mx-auto p-6">
      <h1 class="text-2xl font-bold mb-6 text-blue-800">Apply for Leave</h1>
      <form @submit.prevent="applyLeave" class="bg-white shadow rounded-lg p-6 mb-8 space-y-6">
        <div>
          <label class="block text-gray-700 font-semibold mb-2">Date</label>
          <input v-model="form.date" type="date" class="border border-gray-300 p-2 rounded w-full focus:ring-2 focus:ring-blue-400" required />
        </div>
        <div>
          <label class="block text-gray-700 font-semibold mb-2">Leave Type</label>
          <select v-model="form.leave_type" class="border border-gray-300 p-2 rounded w-full focus:ring-2 focus:ring-blue-400" required>
            <option value="" disabled>Select type</option>
            <option value="paid">Paid Leave</option>
            <option value="unpaid">Unpaid Leave</option>
            <option value="sick">Sick Leave</option>
          </select>
        </div>
        <div>
          <label class="block text-gray-700 font-semibold mb-2">Reason</label>
          <textarea v-model="form.reason" placeholder="Reason" class="border border-gray-300 p-2 rounded w-full focus:ring-2 focus:ring-blue-400" required></textarea>
        </div>
        <div class="text-right">
          <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold shadow">Apply</button>
        </div>
      </form>

      <h2 class="text-xl font-semibold mb-4 text-gray-800">Leave History</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white shadow rounded-lg">
          <thead class="bg-blue-100">
            <tr>
              <th class="py-2 px-4 text-left font-semibold text-gray-700">Date</th>
              <th class="py-2 px-4 text-left font-semibold text-gray-700">Type</th>
              <th class="py-2 px-4 text-left font-semibold text-gray-700">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="leave in leaves" :key="leave.id" class="border-b hover:bg-blue-50">
              <td class="py-2 px-4">{{ leave.date }}</td>
              <td class="py-2 px-4 capitalize">{{ leave.leave_type }}</td>
              <td class="py-2 px-4">
                <span :class="statusClass(leave.status)">{{ leave.status }}</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </EmployeeMainLayout>
</template>


<script setup>
import EmployeeMainLayout from '../Layouts/EmployeeMainLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  leaves: Array,
  stats: Object
});

const form = useForm({
  date: '',
  leave_type: '',
  reason: ''
});

const applyLeave = () => {
  form.post(route('employee.leaves.apply'));
};

function statusClass(status) {
  if (status === 'Accept') return 'bg-green-100 text-green-700 px-2 py-1 rounded';
  if (status === 'Reject') return 'bg-red-100 text-red-700 px-2 py-1 rounded';
  return 'bg-gray-100 text-gray-700 px-2 py-1 rounded';
}
</script>
