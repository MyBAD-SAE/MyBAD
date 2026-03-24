<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import SessionPicker from '@/Components/dashboard/SessionPicker.vue';
import { PartyPopper } from 'lucide-vue-next';

defineProps({
    firstName: { type: String, required: true },
    avatarUrl: { type: String, default: null },
});

const selectedDay = ref('Mardi');
</script>

<template>
    <div class="flex items-start justify-between">
        <div>
            <h1 class="font-semibold text-foreground" style="font-size: 21px;">Bonjour {{ firstName }} <PartyPopper class="inline h-5 w-5 -translate-y-0.5 text-amber-400" /></h1>
            <div class="mt-2">
                <SessionPicker v-model="selectedDay" />
            </div>
        </div>
        <Link :href="route('player.account.index')">
            <Avatar class="h-10 w-10">
                <AvatarImage v-if="avatarUrl" :src="avatarUrl" alt="Avatar" />
                <AvatarFallback>{{ firstName?.charAt(0) }}</AvatarFallback>
            </Avatar>
        </Link>
    </div>
</template>
