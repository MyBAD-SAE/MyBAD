<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        required: true,
    },
});

const styles = computed(() => {
    const map = {
        pending: 'bg-yellow-100 text-yellow-800',
        accepted: 'bg-blue-100 text-blue-800',
        completed: 'bg-green-100 text-green-800',
        declined: 'bg-red-100 text-red-800',
        cancelled: 'bg-gray-100 text-gray-800',
    };
    return map[props.status] || 'bg-gray-100 text-gray-800';
});

const labels = {
    pending: 'Pending',
    accepted: 'In Progress',
    completed: 'Completed',
    declined: 'Declined',
    cancelled: 'Cancelled',
};
</script>

<template>
    <span
        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
        :class="styles"
    >
        {{ labels[status] || status }}
    </span>
</template>
