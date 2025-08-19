<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import axios from 'axios'
import MainLayout from '@/Layouts/MainLayout.vue'
import debounce from 'lodash/debounce'

// --- Page props (expects paginator at page.props.attendances) ---
const page = usePage()
const attendancesProp = computed(() => page.props.attendances ?? null)
const initialDate = ref(page.props.date ?? '') // controller returns 'date'

// --- Build editable records from paginator.data ---
function attendanceArray(val) {
  if (!val) return []
  if (val.data && Array.isArray(val.data)) return val.data
  if (Array.isArray(val)) return val
  return []
}

function buildRecord(att) {
  const employee_id = att.employee_id ?? (att.employee && att.employee.id) ?? att.id ?? ''
  const date = att.date ?? ''
  const timeInRaw = att.time_in ?? null
  const timeOutRaw = att.time_out ?? null

  return {
    employee_id,
    date,
    time_in_str: timeInRaw ? (timeInRaw.length === 5 ? `${timeInRaw}:00` : timeInRaw) : '',
    time_out_str: timeOutRaw ? (timeOutRaw.length === 5 ? `${timeOutRaw}:00` : timeOutRaw) : '',
    status: att.status ?? 'Absent',
  }
}

const form = ref({
  records: attendanceArray(attendancesProp.value).map(buildRecord).filter(r => r.employee_id)
})

// keep records synced when server changes (pagination / filter)
watch(attendancesProp, (val) => {
  form.value.records = attendanceArray(val).map(buildRecord).filter(r => r.employee_id)
})

// --- time helpers (24-hour guaranteed) ---
function pad(n) { return String(n).padStart(2, '0') }

// parse "HH:mm:ss" or "HH:mm" (always return hh,mm,ss as strings padded)
function parseTimeStr(timeStr) {
  if (!timeStr) return { hh: '00', mm: '00', ss: '00' }
  const parts = timeStr.split(':')
  const hh = pad(Number(parts[0] ?? 0))
  const mm = pad(Number(parts[1] ?? 0))
  const ss = pad(Number(parts[2] ?? 0))
  return { hh, mm, ss }
}

function buildTimeStr(hh, mm, ss) {
  return `${pad(hh)}:${pad(mm)}:${pad(ss)}`
}

// when a select changes, update the proper string field on the record
function onTimePartChange(record, side, hh, mm, ss) {
  const value = buildTimeStr(hh, mm, ss)
  if (side === 'time_in') record.time_in_str = value
  else record.time_out_str = value
}

// convenience arrays used in template selects
const HOURS = Array.from({ length: 24 }, (_, i) => pad(i))
const MINS_SECS = Array.from({ length: 60 }, (_, i) => pad(i))

// retained utility (if you still receive input from native time fields elsewhere)
function to24Hour(value){
  if(!value) return '';
  const parts = value.split(':')
  if(parts.length === 2) {
    return `${parts[0].padStart(2,'0')}:${parts[1].padStart(2,'0')}:00`
  }
  return `${parts[0].padStart(2,'0')}:${(parts[1]||'00').padStart(2,'0')}:${(parts[2]||'00').padStart(2,'0')}`
}

// --- saving state & actions ---
const savingRows = reactive({})
const savingAll = ref(false)
function setRowSaving(key, val) { savingRows[key] = val }
function isRowSaving(key) { return !!savingRows[key] }

async function saveRow(record) {
  const key = `${record.employee_id}_${record.date}`
  try {
    setRowSaving(key, true)
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
  } catch (e) {
    console.error(e)
    alert('Error updating this record.')
  } finally {
    setRowSaving(key, false)
  }
}

async function saveAll() {
  try {
    savingAll.value = true
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
  } catch (e) {
    console.error(e)
    alert('Error updating all attendance.')
  } finally {
    savingAll.value = false
  }
}

// --- FILTER (single date) ---
// debounced so quick changes don't spam requests
const date = ref(initialDate.value)
const applyFilter = debounce((val) => {
  const params = {}
  if (val) params.date = val
  router.get(route('attendance'), params, { preserveState: true, replace: true })
}, 300)

watch(date, (val) => applyFilter(val))

// --- PAGINATION: parse page from link.url and call router.get with current date ---
function parsePageFromUrl(url) {
  try {
    const u = new URL(url, window.location.origin)
    const params = new URLSearchParams(u.search)
    return params.get('page') ?? null
  } catch (e) {
    return null
  }
}

function goToLink(link) {
  if (!link || !link.url) return
  const pageNum = parsePageFromUrl(link.url)
  const params = {}
  if (pageNum) params.page = pageNum
  if (date.value) params.date = date.value
  router.get(route('attendance'), params, { preserveState: true, replace: true })
}

// convenience computed values for template
const paginatorLinks = computed(() => {
  const v = attendancesProp.value
  if(!v) return []

  if(Array.isArray(v.links)) return v.links
  
  if(v.meta && Array.isArray(v.meta.links)) return v.meta.links

  return []

})

const recordsCount = computed(() => attendanceArray(attendancesProp.value).length)

// debug
onMounted(() => {
  console.log('Attendance page props snapshot:', page.props)
})
</script>

<template>
  <MainLayout>
    <div class="p-6 space-y-6">
      <h1 class="text-2xl font-bold mb-4">Attendance Records</h1>

      <!-- Date filter -->
      <div class="flex gap-4 items-center mb-4">
        <label class="font-medium">Filter by date:</label>
        <input type="date" v-model="date" class="border rounded p-2" />
        <button v-if="date" @click="date = ''" class="text-sm text-gray-600 hover:underline">Clear</button>
      </div>

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
          <tr v-for="record in form.records" :key="record.employee_id + '_' + record.date" class="text-sm">
            <td class="p-2 border">{{ record.employee_id }}</td>
            <td class="p-2 border">{{ record.date }}</td>

            <!-- Time In: three selects for HH / mm / ss to guarantee 24-hour format -->
            <td class="p-2 border">
              <div class="flex space-x-1 items-center">
                <select
                  class="border rounded p-1 w-18"
                  :value="parseTimeStr(record.time_in_str).hh"
                  @change="e => {
                    const parts = parseTimeStr(record.time_in_str)
                    onTimePartChange(record, 'time_in', e.target.value, parts.mm, parts.ss)
                  }"
                >
                  <option v-for="h in HOURS" :key="h" :value="h">{{ h }}</option>
                </select>

                <select
                  class="border rounded p-1 w-18"
                  :value="parseTimeStr(record.time_in_str).mm"
                  @change="e => {
                    const parts = parseTimeStr(record.time_in_str)
                    onTimePartChange(record, 'time_in', parts.hh, e.target.value, parts.ss)
                  }"
                >
                  <option v-for="m in MINS_SECS" :key="m" :value="m">{{ m }}</option>
                </select>

                <select
                  class="border rounded p-1 w-18"
                  :value="parseTimeStr(record.time_in_str).ss"
                  @change="e => {
                    const parts = parseTimeStr(record.time_in_str)
                    onTimePartChange(record, 'time_in', parts.hh, parts.mm, e.target.value)
                  }"
                >
                  <option v-for="s in MINS_SECS" :key="s" :value="s">{{ s }}</option>
                </select>

                <button
                  class="ml-2 text-sm text-gray-600"
                  type="button"
                  @click="record.time_in_str = ''"
                  title="Clear"
                >✕</button>
              </div>
            </td>

            <!-- Time Out: three selects for HH / mm / ss -->
            <td class="p-2 border">
              <div class="flex space-x-1 items-center">
                <select
                  class="border rounded p-1 w-18"
                  :value="parseTimeStr(record.time_out_str).hh"
                  @change="e => {
                    const parts = parseTimeStr(record.time_out_str)
                    onTimePartChange(record, 'time_out', e.target.value, parts.mm, parts.ss)
                  }"
                >
                  <option v-for="h in HOURS" :key="h" :value="h">{{ h }}</option>
                </select>

                <select
                  class="border rounded p-1 w-18"
                  :value="parseTimeStr(record.time_out_str).mm"
                  @change="e => {
                    const parts = parseTimeStr(record.time_out_str)
                    onTimePartChange(record, 'time_out', parts.hh, e.target.value, parts.ss)
                  }"
                >
                  <option v-for="m in MINS_SECS" :key="m" :value="m">{{ m }}</option>
                </select>

                <select
                  class="border rounded p-1 w-18"
                  :value="parseTimeStr(record.time_out_str).ss"
                  @change="e => {
                    const parts = parseTimeStr(record.time_out_str)
                    onTimePartChange(record, 'time_out', parts.hh, parts.mm, e.target.value)
                  }"
                >
                  <option v-for="s in MINS_SECS" :key="s" :value="s">{{ s }}</option>
                </select>

                <button
                  class="ml-2 text-sm text-gray-600"
                  type="button"
                  @click="record.time_out_str = ''"
                  title="Clear"
                >✕</button>
              </div>
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
                :disabled="isRowSaving(record.employee_id + '_' + record.date)"
                class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 flex items-center gap-2"
              >
                <svg v-if="isRowSaving(record.employee_id + '_' + record.date)" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span v-if="!isRowSaving(record.employee_id + '_' + record.date)">Save</span>
                <span v-else>Saving...</span>
              </button>
            </td>
          </tr>

          <tr v-if="!recordsCount">
            <td class="p-4 border text-center" colspan="6">No attendance records found.</td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="mt-4 flex justify-center" v-if="paginatorLinks && paginatorLinks.length >= 1">
        <template v-for="link in paginatorLinks" :key="link.label">
          <button
            v-if="link.url"
            @click="goToLink(link)"
            v-html="link.label"
            class="mx-1 px-3 py-1 border rounded bg-white hover:bg-gray-100"
          />
          <span v-else v-html="link.label" class="mx-1 px-3 py-1 border rounded text-gray-400" />
        </template>
      </div>

      <!-- Save all -->
      <div class="mt-6">
        <button
          @click="saveAll"
          class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 flex items-center gap-2"
          :disabled="savingAll"
        >
          <svg v-if="savingAll" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
          </svg>
          <span v-if="!savingAll">Save All</span>
          <span v-else>Saving...</span>
        </button>
      </div>
    </div>
  </MainLayout>
  
</template>
<style setup>
    /* Hide native select arrow */
select {
  -webkit-appearance: none; /* Chrome, Safari */
  -moz-appearance: none;    /* Firefox */
  appearance: none;         /* Standard */
  background-image: none !important;
}

/* Optional: add your own minimal style */
select {
  padding-right: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 0.375rem;
  background-color: white;
}

  </style>  
