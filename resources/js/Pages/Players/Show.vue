<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UserAvatar from '@/Components/UserAvatar.vue';
import MatchCard from '@/Components/MatchCard.vue';
import StatCard from '@/Components/StatCard.vue';
import EmptyState from '@/Components/EmptyState.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    player: Object,
    matches: Array,
    rank: Number,
    h2h: Object,
});

const currentUser = usePage().props.auth.user;

function winRate() {
    if (props.player.matches_played === 0) return '0';
    return ((props.player.matches_won / props.player.matches_played) * 100).toFixed(1);
}
</script>

<template>
    <Head :title="player.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Profil joueur</h2>
                <Link
                    v-if="currentUser.id !== player.id"
                    :href="route('matches.create', { opponent: player.id })"
                >
                    <PrimaryButton>Defier</PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">
                <!-- Player Info -->
                <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100 flex flex-col sm:flex-row items-center gap-6">
                    <UserAvatar :user="player" size="xl" />
                    <div class="text-center sm:text-left">
                        <h3 class="text-2xl font-bold text-gray-900">{{ player.name }}</h3>
                        <p class="text-lg text-primary-600 font-semibold">{{ player.elo_rating }} ELO</p>
                        <p class="text-sm text-gray-500">Rang #{{ rank }}</p>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                    <StatCard title="Matchs joues" :value="player.matches_played" color="blue" />
                    <StatCard title="Victoires" :value="player.matches_won" color="green" />
                    <StatCard title="Defaites" :value="player.matches_lost" color="red" />
                    <StatCard title="Win Rate" :value="`${winRate()}%`" color="yellow" />
                </div>

                <!-- H2H -->
                <div v-if="h2h" class="rounded-xl bg-white p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Face-a-face</h3>
                    <div class="flex items-center gap-6">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600">{{ h2h.wins }}</p>
                            <p class="text-xs text-gray-500">Vos victoires</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-400">{{ h2h.played }}</p>
                            <p class="text-xs text-gray-500">Matchs joues</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-red-600">{{ h2h.losses }}</p>
                            <p class="text-xs text-gray-500">Ses victoires</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Matches -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Historique des matchs</h3>
                    <div v-if="matches.length > 0" class="grid gap-3 sm:grid-cols-2">
                        <MatchCard
                            v-for="match in matches"
                            :key="match.id"
                            :match="match"
                            :current-user-id="currentUser.id"
                        />
                    </div>
                    <EmptyState v-else title="Aucun match" description="Ce joueur n'a pas encore joue." />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
