<script setup>
import { Link } from '@inertiajs/vue3';
import UserAvatar from '@/Components/UserAvatar.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
    player: {
        type: Object,
        required: true,
    },
    showChallenge: {
        type: Boolean,
        default: true,
    },
    currentUserId: {
        type: Number,
        default: null,
    },
});
</script>

<template>
    <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100 text-center">
        <Link :href="route('players.show', player.id)">
            <UserAvatar :user="player" size="lg" class="mx-auto" />
            <h3 class="mt-3 text-sm font-semibold text-gray-900">{{ player.name }}</h3>
        </Link>
        <p class="text-lg font-bold text-primary-600 mt-1">{{ player.elo_rating }} ELO</p>
        <p class="text-xs text-gray-400 mt-1">
            {{ player.matches_won }}W - {{ player.matches_lost }}L
        </p>
        <Link
            v-if="showChallenge && currentUserId !== player.id"
            :href="route('matches.create', { opponent: player.id })"
            class="mt-4 inline-flex items-center rounded-md border border-transparent bg-primary-600 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-primary-700"
        >
            Challenge
        </Link>
    </div>
</template>
