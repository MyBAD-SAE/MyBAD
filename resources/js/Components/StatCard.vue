<script setup>
defineProps({
    title: String,
    value: [String, Number],
    subtitle: {
        type: String,
        default: null,
    },
    color: {
        type: String,
        default: 'primary',
    },
});

const colorMap = {
    primary: 'text-primary-600',
    green: 'text-green-600',
    red: 'text-red-600',
    yellow: 'text-yellow-600',
    blue: 'text-blue-600',
    gray: 'text-gray-600',
};
</script>

<template>
    <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100">
        <p class="text-sm font-medium text-gray-500">{{ title }}</p>
        <p class="mt-1 text-3xl font-bold" :class="colorMap[color] || 'text-primary-600'">{{ value }}</p>
        <p v-if="subtitle" class="mt-1 text-sm text-gray-400">{{ subtitle }}</p>
    </div>
</template>
