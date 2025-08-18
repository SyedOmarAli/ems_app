<template>
  <MainLayout>
    <h2 class="text-2xl font-bold mb-4">Edit Employee</h2>
    <Card>
      <form @submit.prevent="updateEmployee" class="space-y-4">
        <div>
          <label>Name</label>
          <input v-model="form.name" class="w-full border p-2 rounded" />
          <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
        </div>

        <div>
          <label>Email</label>
          <input v-model="form.email" class="w-full border p-2 rounded" />
          <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
        </div>

        <div>
          <label>Phone</label>
          <input v-model="form.phone" class="w-full border p-2 rounded" />
          <div v-if="form.errors.phone" class="text-red-500 text-sm mt-1">{{ form.errors.phone }}</div>
        </div>

        <div>
          <label>Salary</label>
          <input v-model="form.salary" type="number" class="w-full border p-2 rounded" />
          <div v-if="form.errors.salary" class="text-red-500 text-sm mt-1">{{ form.errors.salary }}</div>
        </div>

        <div>
          <label>Hire Date</label>
          <input v-model="form.hire_date" type="date" class="w-full border p-2 rounded" />
          <div v-if="form.errors.hire_date" class="text-red-500 text-sm mt-1">{{ form.errors.hire_date }}</div>
        </div>

        <div>
          <label>Code</label>
          <input v-model="form.code" type="text" class="w-full border p-2 rounded" />
          <div v-if="form.errors.code" class="text-red-500 text-sm mt-1">{{ form.errors.code }}</div>
        </div>

        <div>
          <label>Department</label>
          <input v-model="form.department" class="w-full border p-2 rounded" />
          <div v-if="form.errors.department" class="text-red-500 text-sm mt-1">{{ form.errors.department }}</div>
        </div>

        <div class="flex items-center gap-2">
          <button
            type="submit"
            :disabled="form.processing"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50"
          >
            <span v-if="!form.processing">Update Employee</span>
            <span v-else>Updating...</span>
          </button>

          <button type="button" @click="cancel" class="text-sm text-gray-600 hover:underline">
            Cancel
          </button>
        </div>
      </form>
    </Card>
  </MainLayout>
</template>

<script setup>
import { computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import Card from '@/Components/Card.vue'

const props = defineProps({
  employee: Object,
})

// init only fields we want to send
const form = useForm({
  name: props.employee?.name ?? '',
  email: props.employee?.email ?? '',
  phone: props.employee?.phone ?? '',
  salary: props.employee?.salary ?? '',
  hire_date: props.employee?.hire_date ?? '',
  code: props.employee?.code ?? '',
  department: props.employee?.department ?? '',
})

// use admin route names
function updateEmployee() {
  form.put(route('admin.employee.update', { employee: props.employee.id }), {
    preserveState: true,
    onSuccess: () => {
      // default Inertia redirect happens if server returns redirect
      // optionally show toast here
    },
    onError: (errors) => {
      console.log('Validation errors', errors)
    }
  })
}

function cancel() {
  router.visit(route('admin.employee.index'))
}
</script>
