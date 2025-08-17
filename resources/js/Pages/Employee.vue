  <template>
    <MainLayout>
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold">Employees</h2>
        <template>
          <input type="text" v-model="search" placeholder="Search..."></input>
        </template>
        <Link :href="route('employee.create')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        + Create Employee
        </Link>
      </div>
      <Card>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div v-for="employee in employees?.data || []"  :key="employee.id" class="p-4 bg-white shadow rounded">
            <h3 class="text-lg font-semibold">{{ employee.name }}</h3>
            <p>Email: {{ employee.email }}</p>
            <p>Department: {{ employee.department }}</p>
            <p>Hired On: <b>{{ employee.hire_date }}</b></p>
            <p>Current Role: <b>{{ employee.role }}</b></p>
            <!-- Add more fields as needed -->
            <!-- Action Buttons -->
            <div class="flex space-x-2 mt-4">
              <!-- <Link :href="route('employee.show', employee.id)"
                class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
              View
              </Link> -->

              <Link
              v-if="employee?.id"
              :href="route('employee.edit', { employee: employee.id })"
                class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500">
              Edit
              </Link>
              <button @click="deleteEmployee(employee.id)"
                class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                Delete
              </button>
            </div>
          </div>
        </div>
      </Card>
      <div class="mt-4" v-if="employees?.links?.length">
        <template v-for="link in employees.links" :key="(link.url || '') + link.label">
          <Link v-if="link.url" :href="link.url" v-html="link.label" :class="{ 'font-bold': link.active }"
            class="px-2 py-1" />
          <span v-else v-html="link.label" class="px-2 py-1 text-grey-500"></span>
        </template>
      </div>
    </MainLayout>
  </template>

<script setup>
import { defineProps } from 'vue'
import { ref, watch } from 'vue'
//import { Inertia } from '@inertiajs/vue3'
//import { debounce } from 'lodash'
import { router, Link } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import Card from '@/Components/Card.vue'
//import { Presence } from 'reka-ui'

//const search = ref('');
//watch (search, debounce((value) => {
  //Inertia.get(route('employee.index'), { search : value }, {PreserveState: true, PreserveScroll: true});
//}, 300));


const props = defineProps({
  employees: {
    type: Object,
    required: true,
  }
})

function deleteEmployee(id) {
  if (confirm('Are you sure you want to delete this employee?')) {
    router.delete(route('employee.destroy', { employee: id }))
  }
}


</script>
