<script setup>
import { computed } from 'vue';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    size: {
        type: String,
        default: 'md',
    },
});

const sizeClasses = computed(() => {
    const map = {
        sm: 'h-8 w-8 text-xs',
        md: 'h-10 w-10 text-sm',
        lg: 'h-14 w-14 text-lg',
        xl: 'h-20 w-20 text-2xl',
    };
    return map[props.size] || map.md;
});

const initials = computed(() => {
    const words = props.user.name?.split(' ') || [];
    return words.slice(0, 2).map(w => w[0]?.toUpperCase()).join('');
});
</script>

<template>
    <div
        class="inline-flex items-center justify-center rounded-full bg-primary-100 text-primary-700 font-semibold shrink-0"
        :class="sizeClasses"
    >
        <img
            v-if="user.avatar"
            :src="user.avatar"
            :alt="user.name"
            class="rounded-full object-cover"
            :class="sizeClasses"
        />
        <span v-else>{{ initials }}</span>
    </div>
</template>
