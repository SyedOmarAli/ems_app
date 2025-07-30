<template>
  <MainLayout>
    <div class="p-6 space-y-6">
      <h1 class="text-2xl font-bold mb-4">Manual Attendance</h1>

      <table class="w-full table-auto border-collapse shadow rounded overflow-hidden">
        <thead class="bg-gray-100">
          <tr>
            <th class="py-2 px-4 border">Employee ID</th>
            <th class="py-2 px-4 border">Date</th>
            <th class="py-2 px-4 border">Time In</th>
            <th class="py-2 px-4 border">Time Out</th>
            <th class="py-2 px-4 border">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="emp in employees" :key="emp.id" class="text-sm">
            <td class="p-2 border">{{ emp.id }}</td>
            <td class="p-2 border">{{ date }}</td>
            <td class="p-2 border">
              <input type="time" v-model="form[emp.id].time_in" class="border rounded p-1 w-full" />
            </td>
            <td class="p-2 border">
              <input type="time" v-model="form[emp.id].time_out" class="border rounded p-1 w-full" />
            </td>
            <td class="p-2 border">
              <select v-model="form[emp.id].status" class="border rounded p-1 w-full">
                <option>Present</option>
                <option>Absent</option>
                <option>Leave</option>
                <option>Late</option>
              </select>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="text-center">
        <button
          class="bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700"
          @click="saveAll"
        >
          Save All Attendance
        </button>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref } from 'vue'
import { route } from 'ziggy-js'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'
import MainLayout from '@/Layouts/MainLayout.vue'

const { employees = [], attendance = [], date = '' } = usePage().props

const form = ref({})

if (Array.isArray(employees) && employees.length > 0) {
  employees.forEach(emp => {
    const record = Array.isArray(attendance)
      ? attendance.find(a => a.employee_id === emp.id)
      : attendance[emp.id]

    form.value[emp.id] = {
      time_in: record?.time_in || '',
      time_out: record?.time_out || '',
      status: record?.status || 'Absent',
    }
  })
}

function ensureSeconds(time) {
  if (!time) return null
  return time.length === 5 ? time + ':00' : time
}

const saveAll = async () => {
  try {
    const records = Object.entries(form.value).map(([id, data]) => ({
      employee_id: id,
      status: data.status,
      time_in: ensureSeconds(data.time_in),
      time_out: ensureSeconds(data.time_out),
    }))

    await axios.post(route('attendance.update_status'), {
      date: date,
      records: records,
    })

    alert('Attendance saved for all employees!')
  } catch (e) {
    console.error(e)
    alert('Error saving attendance')
  }
}
</script>
