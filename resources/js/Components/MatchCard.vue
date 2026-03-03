<script setup>
import { Link } from '@inertiajs/vue3';
import StatusBadge from '@/Components/StatusBadge.vue';
import UserAvatar from '@/Components/UserAvatar.vue';

defineProps({
    match: {
        type: Object,
        required: true,
    },
    currentUserId: {
        type: Number,
        default: null,
    },
});

function scoreDisplay(match) {
    const sets = [];
    if (match.challenger_score_set1 !== null) {
        sets.push(`${match.challenger_score_set1}-${match.challenged_score_set1}`);
    }
    if (match.challenger_score_set2 !== null) {
        sets.push(`${match.challenger_score_set2}-${match.challenged_score_set2}`);
    }
    if (match.challenger_score_set3 !== null) {
        sets.push(`${match.challenger_score_set3}-${match.challenged_score_set3}`);
    }
    return sets.join(' / ');
}
</script>

<template>
    <Link
        :href="route('matches.show', match.id)"
        class="block rounded-xl bg-white p-4 shadow-sm border border-gray-100 hover:border-primary-200 transition"
    >
        <div class="flex items-center justify-between mb-3">
            <StatusBadge :status="match.status" />
            <span v-if="match.played_at" class="text-xs text-gray-400">
                {{ new Date(match.played_at).toLocaleDateString() }}
            </span>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <UserAvatar :user="match.challenger" size="sm" />
                <span
                    class="text-sm font-medium"
                    :class="match.winner_id === match.challenger.id ? 'text-primary-600 font-bold' : 'text-gray-700'"
                >
                    {{ match.challenger.name }}
                </span>
            </div>

            <span class="text-xs font-bold text-gray-400 uppercase">vs</span>

            <div class="flex items-center gap-2">
                <span
                    class="text-sm font-medium"
                    :class="match.winner_id === match.challenged.id ? 'text-primary-600 font-bold' : 'text-gray-700'"
                >
                    {{ match.challenged.name }}
                </span>
                <UserAvatar :user="match.challenged" size="sm" />
            </div>
        </div>

        <div v-if="match.status === 'completed'" class="mt-2 text-center">
            <span class="text-sm font-mono text-gray-600">{{ scoreDisplay(match) }}</span>
            <span v-if="match.elo_change" class="ml-2 text-xs text-gray-400">
                ({{ match.elo_change > 0 ? '+' : '' }}{{ match.elo_change }} ELO)
            </span>
        </div>
    </Link>
</template>
