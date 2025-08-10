<template>
  <div>
    <h1 class="text-2xl font-bold">Employee Dashboard</h1>
    <p>Total Days: {{ stats.total_days }}</p>
    <p>Present: {{ stats.present }}</p>
    <p>Absent: {{ stats.absent }}</p>
    <p>Leaves: {{ stats.leaves }}</p>
  </div>
</template>

<script setup>
defineProps({
  stats: Object
});
</script>
