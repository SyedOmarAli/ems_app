<template>
  <MainLayout>
    <h2 class="text-2xl font-bold mb-4">Add New Employee</h2>

    <Card>
      <form @submit.prevent="submit">
        <div class="mb-4">
          <label for="name" class="block text-gray-700 font-medium mb-1">Name</label>
          <input
            v-model="form.name"
            id="name"
            type="text"
            class="w-full p-2 border border-gray-300 rounded"
          />
          <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
        </div>

        <div class="mb-4">
          <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
          <input
            v-model="form.email"
            id="email"
            type="email"
            class="w-full p-2 border border-gray-300 rounded"
          />
          <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
        </div>

       <div class="mb-4">
        <label for="name" class="block text-gray-700 font-medium mb-1">Phone</label>
          <input
            v-model="form.phone"
            id="phone"
            type="tel"
            class="w-full p-2 border border-gray-300 rounded"
          />
          <div v-if="form.errors.phone" class="text-red-500 text-sm mt-1">{{ form.errors.phone }}</div>
        </div>

        <div class="mb-4">
        <label for="name" class="block text-gray-700 font-medium mb-1">Salary</label>
          <input
            v-model="form.salary"
            id="salary"
            type="number"
            class="w-full p-2 border border-gray-300 rounded"
          />
          <div v-if="form.errors.salary" class="text-red-500 text-sm mt-1">{{ form.errors.salary }}</div>
        </div>

        <div class="mb-4">
        <label for="name" class="block text-gray-700 font-medium mb-1">Hire Date</label>
          <input
            v-model="form.hire_date"
            id="hire_date"
            type="date"
            class="w-full p-2 border border-gray-300 rounded"
          />
          <div v-if="form.errors.hire_date" class="text-red-500 text-sm mt-1">{{ form.errors.hire_date }}</div>
        </div>

        <div class="mb-4">
        <label for="name" class="block text-gray-700 font-medium mb-1">Code</label>
          <input
            v-model="form.code"
            id="code"
            type="text"
            class="w-full p-2 border border-gray-300 rounded"
          />
          <div v-if="form.errors.code" class="text-red-500 text-sm mt-1">{{ form.errors.code }}</div>
        </div>

        <div class="mb-4">
        <label for="name" class="block text-gray-700 font-medium mb-1">Department</label>
          <input
            v-model="form.department"
            id="department"
            type="text"
            class="w-full p-2 border border-gray-300 rounded"
          />
          <div v-if="form.errors.department" class="text-red-500 text-sm mt-1">{{ form.errors.department }}</div>
        </div>

        <button
          type="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          Save Employee
        </button>
      </form>
    </Card>
  </MainLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import Card from '@/Components/Card.vue'

const form = useForm({
  name: '',
  email: '',
  phone: '',
  salary: '',
  hire_date: '',
  code: '',
  department: '',

})

function submit() {
  form.post(route('employee.store'))
}
</script>
