<script setup>
import EmployeeMainLayout from '@/Layouts/EmployeeMainLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
  leaves: Array,
  stats: Object,
});

const form = useForm({
  from_date: '',
  to_date: '',
  leave_type: '',
  reason: '',
  status: '', 
});

function submit() {
  form.post(route('employee.leaves.apply'), {
    onSuccess: () => {
      form.reset();
    },
  });
}
</script>

<template>
    <div v-if="$page.props.flash && $page.props.flash.message" class="mb-4 p-3 bg-green-100 text-green-700 rounded">
    {{ $page.props.flash.message }}
  </div>
  <Head title="My Leaves" />
  <EmployeeMainLayout :employee-name="stats.employee_name">
    <div class="py-10 px-4 md:px-8 bg-gray-50 min-h-screen">
      <h1 class="text-2xl font-bold mb-6 text-gray-800">Apply for Leave</h1>
      <form @submit.prevent="submit" class="bg-white rounded-lg shadow p-6 mb-10 max-w-xl">
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-2">From Date</label>
          <input v-model="form.from_date" type="date" class="input" required />
          <div v-if="form.errors.from_date" class="text-red-500 text-xs mt-1">{{ form.errors.from_date }}</div>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-2">To Date</label>
          <input v-model="form.to_date" type="date" class="input" required />
          <div v-if="form.errors.to_date" class="text-red-500 text-xs mt-1">{{ form.errors.to_date }}</div>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-2">Leave Type</label>
          <select v-model="form.leave_type" class="input" required>
            <option value="" disabled>Select type</option>
            <option value="Sick">Sick</option>
            <option value="Casual">Casual</option>
            <option value="Paid">Paid</option>
            <option value="Unpaid">Unpaid</option>
          </select>
          <div v-if="form.errors.leave_type" class="text-red-500 text-xs mt-1">{{ form.errors.leave_type }}</div>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-2">Reason</label>
          <textarea v-model="form.reason" class="input" rows="3" placeholder="Optional"></textarea>
          <div v-if="form.errors.reason" class="text-red-500 text-xs mt-1">{{ form.errors.reason }}</div>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition">
          Apply
        </button>
      </form>

      <h2 class="text-xl font-semibold mb-4 text-gray-700">My Leave Requests</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-xl shadow">
          <thead>
            <tr class="bg-blue-100 text-blue-700">
              <th class="py-3 px-4 text-left">Type</th>
              <th class="py-3 px-4 text-left">From</th>
              <th class="py-3 px-4 text-left">To</th>
              <th class="py-3 px-4 text-left">Status</th>
              <th class="py-3 px-4 text-left">Reason</th>
              <th class="py-3 px-4 text-left">Applied At</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="leave in leaves" :key="leave.id" class="border-b hover:bg-blue-50 transition">
              <td class="py-3 px-4">{{ leave.leave_type }}</td>
              <td class="py-3 px-4">{{ leave.from_date }}</td>
              <td class="py-3 px-4">{{ leave.to_date }}</td>
              <td class="py-3 px-4">
                <span
                  :class="{
                    'bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs': leave.Status === 'Approved',
                    'bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs': leave.Status === 'Pending',
                    'bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs': leave.Status === 'Rejected',
                  }"
                >
                  {{ leave.status }}
                </span>
              </td>
              <td class="py-3 px-4">{{ leave.reason }}</td>
              <td class="py-3 px-4">{{ leave.created_at }}</td>
            </tr>
            <tr v-if="!leaves || leaves.length === 0">
              <td colspan="6" class="py-6 px-4 text-center text-gray-400">No leave requests found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </EmployeeMainLayout>
</template>

<style scoped>
.input {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border: 1px solid #cbd5e1;
  border-radius: 0.375rem;
  font-size: 1rem;
  background: #f8fafc;
  transition: border 0.2s;
}
.input:focus {
  outline: none;
  border-color: #2563eb;
  background: #fff;
}
</style>