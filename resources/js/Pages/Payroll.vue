<template>
  <MainLayout>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Payroll Management</h1>

      <!-- Generate Payroll Form -->
      <div class="flex items-center space-x-4 mb-6">
        <!-- Month Selector -->
        <input
          v-model="selectedMonth"
          type="month"
          class="border p-2 rounded"
          @change="setDateRange"
        />

        <!-- Start Date -->
        <input v-model="startDate" type="date" class="border p-2 rounded" />

        <!-- End Date -->
        <input v-model="endDate" type="date" class="border p-2 rounded" />

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
            <th class="border p-2">Employee Name</th>
            <th class="border p-2">Total Minutes</th>
            <th class="border p-2">Overtime Minutes</th>
            <th class="border p-2">Hourly Rate</th>
            <th class="border p-2">Total Lates</th>
            <th class="border p-2">Total Absents</th>
            <th class="border p-2">Total Leaves</th>
            <th class="border p-2">Total Deducted Amount</th>
            <th class="border p-2">Total Salary</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="pay in payrolls" :key="pay.id">
            <td class="border p-2">{{ pay.employee?.name || pay.employee_id }}</td>
            <td class="border p-2">{{ pay.total_minutes }}</td>
            <td class="border p-2">{{ pay.overtime }}</td>
            <td class="border p-2">{{ pay.hourly_rate }}</td>
            <td class="border p-2">{{ pay.total_lates }}</td>
            <td class="border p-2">{{ pay.total_absents }}</td>
            <td class="border p-2">{{ pay.total_leaves }}</td>
            <td class="border p-2">{{ pay.total_deduction_amount }}</td>
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

// Props from Inertia
const props = defineProps({
  payrolls: {
    type: Array,
    default: () => [],
  },
})

const payrolls = ref([...props.payrolls])
const selectedMonth = ref('')
const startDate = ref('')
const endDate = ref('')

// Auto-fill startDate and endDate when a month is selected
function setDateRange() {
  if (!selectedMonth.value) return
  const [year, month] = selectedMonth.value.split('-')
  const start = new Date(year, month - 1, 1)
  const end = new Date(year, month, 0) // last day of month
  startDate.value = start.toISOString().slice(0, 10)
  endDate.value = end.toISOString().slice(0, 10)
}

async function generatePayroll() {
  if (!startDate.value || !endDate.value) {
    return alert('Please select start and end dates or a month!')
  }

  try {
    const { data } = await axios.post(route('payroll.generate'), {
      start_date: startDate.value,
      end_date: endDate.value,
    })

    if (data.success) {
      alert(data.message)
      payrolls.value = data.data
    } else {
      alert('Payroll generation failed.')
    }
  } catch (error) {
    console.error(error)
    alert('Error generating payroll.')
  }
}
</script>
