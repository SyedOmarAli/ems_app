<template>
  <MainLayout>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Payroll Management</h1>

      <!-- Generate Payroll Form -->
      <div class="flex items-center space-x-4 mb-6">
        <input
          v-model="selectedMonth"
          type="month"
          class="border p-2 rounded"
        />
        <button
          @click="generatePayroll"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          Generate Payroll
        </button>
      </div>

      <!-- Payroll Table -->
      <table class="w-full border-collapse border border-gray-300">
        <thead>
          <tr class="bg-gray-200">
            <th class="border p-2">Employee ID</th>
            <th class="border p-2">Month</th>
            <th class="border p-2">Total Minutes</th>
            <th class="border p-2">Hourly Rate</th>
            <th class="border p-2">Total Salary</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="pay in payrolls" :key="pay.id">
            <td class="border p-2">{{ pay.employee?.name || pay.employee_id }}</td>
            <td class="border p-2">{{ formatMonth(pay.month) }}</td>
            <td class="border p-2">{{ pay.total_minutes }}</td>
            <td class="border p-2">{{ pay.hourly_rate }}</td>
            <td class="border p-2">{{ pay.total_salary }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import { ref } from 'vue'
import axios from 'axios'

// Props from Inertia (initial payroll list)
const props = defineProps({
  payrolls: {
    type: Array,
    default: () => [],
  },
})

// Reactive state
const payrolls = ref([...props.payrolls])
const selectedMonth = ref(new Date().toISOString().slice(0, 7)) // default to current month

// Generate payroll and update table instantly
async function generatePayroll() {
  if (!selectedMonth.value) return alert('Please select a month!')

  try {
    const { data } = await axios.post(route('payroll.generate'), {
      month: selectedMonth.value,
    })

    if (data.success) {
      alert(data.message)
      payrolls.value = data.data // update table with new payroll data
    } else {
      alert('Payroll generation failed.')
    }
  } catch (error) {
    console.error(error)
    alert('Error generating payroll.')
  }
}

// Format month to "YYYY-MM"
function formatMonth(dateString) {
  return dateString.slice(0, 7)
}
</script>
