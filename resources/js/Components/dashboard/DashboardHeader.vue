<script setup>
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Badge } from '@/Components/ui/badge';
import { CalendarDays, ChevronDown } from 'lucide-vue-next';

defineProps({
    firstName: { type: String, required: true },
    avatarUrl: { type: String, default: null },
    sessionLabel: { type: String, default: 'Séance du mardi' },
});
</script>

<template>
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-foreground">Bonjour {{ firstName }} 👋</h1>
            <button class="mt-2 inline-flex items-center gap-1.5 rounded-full border border-primary/30 bg-primary/5 px-3 py-1 text-sm font-medium text-primary">
                <CalendarDays class="h-3.5 w-3.5" />
                {{ sessionLabel }}
                <ChevronDown class="h-3.5 w-3.5" />
            </button>
        </div>
        <Avatar class="h-10 w-10">
            <AvatarImage v-if="avatarUrl" :src="avatarUrl" alt="Avatar" />
            <AvatarFallback>{{ firstName?.charAt(0) }}</AvatarFallback>
        </Avatar>
    </div>
</template>
