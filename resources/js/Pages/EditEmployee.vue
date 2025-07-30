<template>
  <MainLayout>
    <h2 class="text-2xl font-bold mb-4">Edit Employee</h2>
    <Card>
      <form @submit.prevent="updateEmployee" class="space-y-4">
        <div>
          <label>Name</label>
          <input v-model="form.name" class="w-full border p-2 rounded" />
        </div>

        <div>
          <label>Email</label>
          <input v-model="form.email" class="w-full border p-2 rounded" />
        </div>

        <div>
          <label>Phone</label>
          <input v-model="form.phone" class="w-full border p-2 rounded" />
        </div>

        <div>
          <label>Salary</label>
          <input v-model="form.salary" type="number" class="w-full border p-2 rounded" />
        </div>

        <div>
          <label>Hire Date</label>
          <input v-model="form.hire_date" type="date" class="w-full border p-2 rounded" />
        </div>

        <div>
          <label>Department</label>
          <input v-model="form.department" class="w-full border p-2 rounded" />
        </div>

        <button
          type="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          Update Employee
        </button>
      </form>
    </Card>
  </MainLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { useForm } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import Card from '@/Components/Card.vue'

const props = defineProps({
  employee: Object,
})

// Initialize the form with employee's current values
const form = useForm({ ...props.employee })

function updateEmployee() {
  form.put(route('employee.update', props.employee.id))
}
</script>
