<script setup>
import { computed } from 'vue';

const props = defineProps({
    rank: {
        type: Number,
        required: true,
    },
});

const medal = computed(() => {
    if (props.rank === 1) return { emoji: '🥇', bg: 'bg-yellow-100 text-yellow-800' };
    if (props.rank === 2) return { emoji: '🥈', bg: 'bg-gray-100 text-gray-700' };
    if (props.rank === 3) return { emoji: '🥉', bg: 'bg-orange-100 text-orange-800' };
    return null;
});
</script>

<template>
    <span
        v-if="medal"
        class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-sm font-semibold"
        :class="medal.bg"
    >
        {{ medal.emoji }}
    </span>
    <span v-else class="text-sm text-gray-500 font-medium">#{{ rank }}</span>
</template>
