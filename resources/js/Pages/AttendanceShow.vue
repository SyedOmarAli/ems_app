<template>
  <MainLayout>
    <div class="p-6 space-y-6">
      <h1 class="text-2xl font-bold mb-4">Attendance Records</h1>

      <table class="w-full table-auto border-collapse shadow rounded overflow-hidden">
        <thead class="bg-gray-100">
          <tr>
            <th class="py-2 px-4 border">Employee ID</th>
            <th class="py-2 px-4 border">Date</th>
            <th class="py-2 px-4 border">Time In</th>
            <th class="py-2 px-4 border">Time Out</th>
            <th class="py-2 px-4 border">Status</th>
            <th class="py-2 px-4 border">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(record, index) in form.records" :key="index" class="text-sm">
            <td class="p-2 border">{{ record.employee_id }}</td>
            <td class="p-2 border">{{ record.date }}</td>
            <td class="p-2 border">
              <input type="time" v-model="record.time_in" class="border rounded p-1 w-full" />
            </td>
            <td class="p-2 border">
              <input type="time" v-model="record.time_out" class="border rounded p-1 w-full" />
            </td>
            <td class="p-2 border">
              <select v-model="record.status" class="border rounded p-1 w-full">
                <option>Present</option>
                <option>Absent</option>
                <option>Leave</option>
                <option>Late</option>
              </select>
            </td>
            <td class="p-2 border">
              <button
                @click="saveRow(record)"
                class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700"
              >
                Save
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <button
        @click="saveAll"
        class="mt-4 bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700"
      >
        Save All
      </button>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import axios from 'axios'
import MainLayout from '@/Layouts/MainLayout.vue'

const { attendances = [] } = usePage().props

const form = ref({
  records: attendances.map(att => ({
    employee_id: att.employee_id,
    date: att.date,
    time_in: att.time_in ? att.time_in.slice(0, 5) : '',
    time_out: att.time_out ? att.time_out.slice(0, 5) : '',
    status: att.status || 'Absent'
  }))
})

async function saveAll() {
  try {
    await axios.post(route('attendance.update_status'), form.value)
    alert('All attendance records updated successfully!')
  } catch (e) {
    console.error(e)
    alert('Error updating all attendance.')
  }
}

async function saveRow(record) {
  try {
    await axios.post(route('attendance.update_status'), { records: [record] })
    alert(`Attendance for Employee ${record.employee_id} updated!`)
  } catch (e) {
    console.error(e)
    alert('Error updating this record.')
  }
}
</script>
