<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    title: { type: String, required: true },
    description: { type: String, required: true },
    icon: { type: Object, required: true },
    badge: { type: Number, default: null },
    href: { type: String, default: '#' },
    highlighted: { type: Boolean, default: false },
});
</script>

<template>
    <Link
        :href="href"
        class="group relative flex flex-col rounded-2xl border bg-white p-5 transition-all hover:shadow-md"
        :class="highlighted && 'border-primary/30 border-dashed'"
    >
        <div class="flex items-start justify-between">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl" :class="highlighted ? 'bg-primary/10' : 'bg-gray-100'">
                <component :is="icon" class="h-5 w-5" :class="highlighted ? 'text-primary' : 'text-muted-foreground'" />
            </div>
            <span
                v-if="badge !== null"
                class="inline-flex h-6 min-w-6 items-center justify-center rounded-full bg-gray-100 px-2 text-xs font-semibold text-foreground"
            >
                {{ badge }}
            </span>
        </div>
        <h3 class="mt-4 text-sm font-semibold text-foreground">{{ title }}</h3>
        <p class="mt-1 text-xs text-muted-foreground">{{ description }}</p>
    </Link>
</template>
