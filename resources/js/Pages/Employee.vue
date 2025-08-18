<script setup>
import { ref, watch, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import debounce from 'lodash/debounce'
import MainLayout from '@/Layouts/MainLayout.vue'
import Card from '@/Components/Card.vue'

// Keep props reactive
const page = usePage()
const employees = computed(() => page.props.employees ?? { data: [], links: [] })
const initialSearch = computed(() => page.props.search ?? '')

const searchTerm = ref(initialSearch.value)

const performSearch = debounce((val) => {
  router.get(route('admin.employee.index'), { search: val },
    { preserveState: true, replace: true })
}, 500)


watch(searchTerm, (val) => {
  performSearch(val)
})

function deleteEmployee(id) {
  if (confirm('Are you sure you want to delete this employee?')) {
    router.delete(route('admin.employee.destroy', { employee: id }))
  }
}

function goToLink(link) {
  if (!link || !link.url) return

  const url = link.url
  const separator = url.includes('?') ? '&' : '?'
  const searchParam = searchTerm.value ? `${separator}search=${encodeURIComponent(searchTerm.value)}` : ''
  router.get(url + searchParam, {}, { preserveState: true, replace: true })
}
</script>

<template>
  <MainLayout>
    <div class="flex justify-between items-center mb-4">
      <input v-model="searchTerm" type="text" placeholder="Search employees..."
        class="border rounded px-3 py-2 w-2/3" />
      <button @click="router.visit(route('admin.employee.create'))"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">
        + Create Employee
      </button>
    </div>

    <Card>
      <table class="w-full border rounded shadow">
        <thead class="bg-gray-100">
          <tr>
            <th class="py-2 px-4 border">Name</th>
            <th class="py-2 px-4 border">Email</th>
            <th class="py-2 px-4 border">Department</th>
            <th class="py-2 px-4 border">Phone</th>
            <th class="py-2 px-4 border">Salary</th>
            <th class="py-2 px-4 border">Hire Date</th>
            <th class="py-2 px-4 border">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="employee in employees.data" :key="employee.id">
            <td class="p-2 border">{{ employee.name }}</td>
            <td class="p-2 border">{{ employee.email }}</td>
            <td class="p-2 border">{{ employee.department }}</td>
            <td class="p-2 border">{{ employee.phone }}</td>
            <td class="p-2 border">{{ employee.salary }}</td>
            <td class="p-2 border">{{ employee.hire_date }}</td>
            <td class="p-2 border">
            <td class="flex">
              <button @click="router.visit(route('admin.employee.edit', { employee: employee.id }))"
                class="bg-silver-500 text-white px-3 py-1 rounded mr-2 hover:bg-yellow-600">
                <font-awesome-icon :icon="['fas', 'pen']" class="cursor-pointer text-yellow-500" />
              </button>

              <button @click="router.visit(route('admin.employee.show', employee.id))"
                class="bg-silver-500 text-white px-3 py-1 rounded mr-2 hover:bg-green-600">
                <font-awesome-icon :icon="['fas', 'eye']" class="cursor-pointer text-yellow-500" />
              </button>

              <button @click="deleteEmployee(employee.id)"
                class="bg-silver-600 text-white px-3 py-1 rounded hover:bg-red-700"><font-awesome-icon
                  :icon="['fas', 'trash']" class="cursor-pointer text-yellow-500" /></button>

            </td>

            </td>
          </tr>
        </tbody>
      </table>
    </Card>

    <!-- Pagination -->
    <div class="mt-4 flex justify-center" v-if="employees?.links?.length">
      <template v-for="link in employees.links" :key="link.label">
        <button v-if="link.url" @click="goToLink(link)" v-html="link.label"
          class="mx-1 px-3 py-1 border rounded bg-white hover:bg-gray-100" />
        <span v-else v-html="link.label" class="mx-1 px-3 py-1 border rounded text-gray-400" />
      </template>
    </div>
  </MainLayout>
</template>
