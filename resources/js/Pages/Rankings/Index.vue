<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UserAvatar from '@/Components/UserAvatar.vue';
import RankBadge from '@/Components/RankBadge.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';

defineProps({
    players: Array,
});

const currentUser = usePage().props.auth.user;

function winRate(player) {
    if (player.matches_played === 0) return '0';
    return ((player.matches_won / player.matches_played) * 100).toFixed(1);
}
</script>

<template>
    <Head title="Classement" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Classement</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-xl bg-white shadow-sm border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joueur</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">ELO</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">V/D</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Win Rate</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr
                                v-for="(player, index) in players"
                                :key="player.id"
                                :class="player.id === currentUser.id ? 'bg-primary-50' : 'bg-white'"
                                class="hover:bg-gray-50 transition"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <RankBadge :rank="index + 1" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <Link :href="route('players.show', player.id)" class="flex items-center gap-3 group">
                                        <UserAvatar :user="player" size="sm" />
                                        <span class="text-sm font-medium text-gray-900 group-hover:text-primary-600">
                                            {{ player.name }}
                                            <span v-if="player.id === currentUser.id" class="text-xs text-primary-500">(vous)</span>
                                        </span>
                                    </Link>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm font-bold text-primary-600">{{ player.elo_rating }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center hidden sm:table-cell">
                                    <span class="text-sm text-gray-600">
                                        <span class="text-green-600">{{ player.matches_won }}</span>
                                        /
                                        <span class="text-red-600">{{ player.matches_lost }}</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center hidden sm:table-cell">
                                    <span class="text-sm text-gray-600">{{ winRate(player) }}%</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
