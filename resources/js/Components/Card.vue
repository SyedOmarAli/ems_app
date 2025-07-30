<template>
  <div class="bg-white rounded-xl shadow p-4">
    <h3 class="text-lg font-semibold">{{ title }}</h3>
    <p class="text-2xl font-bold">{{ value }}</p>
    <slot />
  </div>
</template>

<script setup>
defineProps({
  title: String,
  value: [String, Number]
})
</script>
