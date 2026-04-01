<script setup>
import { Link } from '@inertiajs/vue3';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar/index.ts';
import ClassPicker from '@/Components/player/dashboard/ClassPicker.vue';
import { PartyPopper } from 'lucide-vue-next';

defineProps({
    firstName: { type: String, required: true },
    avatarUrl: { type: String, default: null },
    classes: { type: Array, default: () => [] },
    selectedClassId: { type: Number, default: null },
});
</script>

<template>
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-foreground">Bonjour {{ firstName }} <PartyPopper class="inline h-5 w-5 -translate-y-0.5 text-amber-400" /></h1>
            <div v-if="classes.length > 1" class="mt-2">
                <ClassPicker :classes="classes" :selected-class-id="selectedClassId" />
            </div>
            <div v-else class="mt-1" />
        </div>
        <Link :href="route('player.account.index')">
            <Avatar class="h-10 w-10">
                <AvatarImage v-if="avatarUrl" :src="avatarUrl" alt="Avatar" />
                <AvatarFallback>{{ firstName?.charAt(0) }}</AvatarFallback>
            </Avatar>
        </Link>
    </div>
</template>
