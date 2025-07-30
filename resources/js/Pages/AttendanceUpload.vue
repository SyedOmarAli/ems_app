<template>
  <MainLayout>
  <div class="max-w-lg mx-auto mt-10 p-6 bg-white rounded shadow">
    <h1 class="text-xl font-bold mb-4">Upload Attendance</h1>

    <form @submit.prevent="submit">
      <div class="mb-4">
        <label class="block mb-2 font-semibold" for="file">Choose Attendance File</label>
        <input
          type="file"
          id="file"
          @change="handleFileChange"
          class="w-full border rounded p-2"
          required
        />
      </div>

      <button
        type="submit"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50"
        :disabled="form.processing"
      >
        {{ form.processing ? "Uploading..." : "Upload" }}
      </button>
    </form>

    <div v-if="form.errors.file" class="mt-2 text-red-600">
      {{ form.errors.file }}
    </div>
  </div>

  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const form = useForm({
  file: null,
})

function handleFileChange(event) {
  form.file = event.target.files[0]
}

function submit() {
  form.post(route('attendance.upload'), {
    forceFormData: true,
    onSuccess: () => {
      alert('Attendance uploaded successfully!')
    },
  })
}
</script>
