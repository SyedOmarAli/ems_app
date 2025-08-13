<template>
  <MainLayout>
    <div class="p-6">
      <h1 class="text-xl font-bold mb-4">Leave Requests</h1>
      <table class="w-full border">
        <thead>
          <tr>
            <th class="border p-2">Employee</th>
            <th class="border p-2">From</th>
            <th class="border p-2">To</th>
            <th class="border p-2">Type</th>
            <th class="border p-2">Reason</th>
            <th class="border p-2">Status</th>
            <th class="border p-2">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="leave in localLeaves" :key="leave.id">
            <td class="border p-2">{{ leave.employee?.name }}</td>
            <td class="border p-2">{{ leave.from_date }}</td>
            <td class="border p-2">{{ leave.to_date }}</td>
            <td class="border p-2">{{ leave.leave_type }}</td>
            <td class="border p-2">{{ leave.reason }}</td>
            <td class="border p-2">
              <span v-if="leave.status === 'Pending'" class="text-yellow-600">Pending</span>
              <span v-else-if="leave.status === 'Approved'" class="text-green-600">Approved</span>
              <span v-else class="text-red-600">Rejected</span>
            </td>
            <td class="border p-2">
              <template v-if="leave.status === 'Pending'">
                <button @click="approveLeave(leave.id)"
                  class="bg-green-500 text-white p-1 rounded mr-2">Approve</button>
                <button @click="rejectLeave(leave.id)" class="bg-red-500 text-white p-1 rounded">Reject</button>
              </template>
              <template v-else>
                <span v-if="leave.status === 'Approved'" class="text-green-600 font-semibold">Approved</span>
                <span v-else-if="leave.status === 'Rejected'" class="text-red-600 font-semibold">Rejected</span>
              </template>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  leaves: Array
});

// Make a local copy to update UI reactively
const localLeaves = ref(props.leaves.map(leave => ({ ...leave })));

const approveLeave = async (id) => {
  await router.post(route('admin.leaves.approve', id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      const leave = localLeaves.value.find(l => l.id === id);
      if (leave) leave.status = 'Approved';
    }
  });
};

const rejectLeave = async (id) => {
  await router.post(route('admin.leaves.reject', id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      const leave = localLeaves.value.find(l => l.id === id);
      if (leave) leave.status = 'Rejected';
    }
  });
};
</script>