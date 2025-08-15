<script setup>
import { ref, reactive, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import axios from 'axios'
import MainLayout from '@/Layouts/MainLayout.vue'

const page = usePage()
const rawAttendances = page.props.attendances ?? page.props.attendance ?? []

// Build records but keep both string and Date forms.
// `time_in_date` is the Date object used when editing/picking.
// `time_in_str` is the canonical string to display / save when not editing.
const form = ref({
  records: rawAttendances.map(att => {
    const employee_id = att.employee_id ?? (att.employee && att.employee.id) ?? att.id ?? ''
    const date = att.date ?? ''
    const timeInRaw = att.time_in ?? null
    const timeOutRaw = att.time_out ?? null

    return {
      employee_id,
      date,
      time_in_date: null,   // Date or null
      time_out_date: null, // Date or null
      time_in_str: timeInRaw ? (timeInRaw.length === 5 ? `${timeInRaw}:00` : timeInRaw) : '',
      time_out_str: timeOutRaw ? (timeOutRaw.length === 5 ? `${timeOutRaw}:00` : timeOutRaw) : '',
      status: att.status ?? 'Absent'
    }
  }).filter(r => r.employee_id)
})

// track which pickers are open; use index or employee_id as key
const openPickers = reactive({}) // e.g. openPickers['8_time_in'] = true

function openTimePicker(empId, side) {
  openPickers[`${empId}_${side}`] = true
  // if there is no Date object yet but we have a string, create Date from string
  const rec = form.value.records.find(r => r.employee_id === empId)
  if (!rec) return
  const date = rec.date
  if (side === 'time_in' && !rec.time_in_date && rec.time_in_str) {
    rec.time_in_date = buildDateTime(date, rec.time_in_str)
  }
  if (side === 'time_out' && !rec.time_out_date && rec.time_out_str) {
    rec.time_out_date = buildDateTime(date, rec.time_out_str)
  }
}

function closeTimePicker(empId, side) {
  openPickers[`${empId}_${side}`] = false
  // if user cleared the picker (null), keep string empty
  const rec = form.value.records.find(r => r.employee_id === empId)
  if (!rec) return
  if (side === 'time_in') {
    rec.time_in_str = formatDateToTimeString(rec.time_in_date) || ''
  } else {
    rec.time_out_str = formatDateToTimeString(rec.time_out_date) || ''
  }
}

function to24Hour(value){
  if(!value) return '';
  let parts = value.split(':');
  if(parts.length === 2) {
    return `${parts[0].padStart(2, '0')}:${parts[1].padStart(2, '0')}:00`
  }
  return `${parts[0].padStart(2, '0')}:${parts[1].padStart(2, '0')}:${parts[2].padStart(2, '0')}`;
}

async function saveAll() {
  try {
    const payload = {
      records: form.value.records.map(r => ({
        employee_id: r.employee_id,
        date: r.date,
        status: r.status,
        time_in: r.time_in_str || '',
        time_out: r.time_out_str || ''
      }))
    }
    await axios.post(route('attendance.update_status'), payload)
    alert('All attendance records updated successfully!')
  } catch (e) {
    console.error(e)
    alert('Error updating all attendance.')
  }
}

async function saveRow(record) {
  try {
    const payload = {
      records: [{
        employee_id: record.employee_id,
        date: record.date,
        status: record.status,
        time_in: record.time_in_str || '',
        time_out: record.time_out_str || ''
      }]
    }
    await axios.post(route('attendance.update_status'), payload)
    alert(`Attendance for Employee ${record.employee_id} updated!`)
  } catch (e) {
    console.error(e)
    alert('Error updating this record.')
  }
}

// debug: quickly inspect mapping
onMounted(() => {
  console.log('Mapped attendance records (first 5):', form.value.records.slice(0, 5))
})
</script>



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
              <input
                type="time"
                step="1"
                :value="record.time_in_str"
                @input="e => record.time_in_str = to24Hour(e.target.value)"
                class="border rounded p-1 w-full"
                placeholder="HH:mm:ss"
              />
            </td>
            <td class="p-2 border">
              <input
                type="time"
                step="1"
                :value="record.time_out_str"
                @input="e => record.time_out_str = to24Hour(e.target.value)"
                class="border rounded p-1 w-full"
                placeholder="HH:mm:ss"
              />
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
              <button @click="saveRow(record)" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                Save
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <button @click="saveAll" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">
        Save All
      </button>
    </div>
  </MainLayout>
</template>