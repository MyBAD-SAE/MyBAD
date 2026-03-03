<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import MatchCard from '@/Components/MatchCard.vue';
import UserAvatar from '@/Components/UserAvatar.vue';
import EmptyState from '@/Components/EmptyState.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
    pendingChallenges: Array,
    recentMatches: Array,
});

const user = usePage().props.auth.user;

function acceptChallenge(matchId) {
    router.post(route('matches.accept', matchId));
}

function declineChallenge(matchId) {
    router.post(route('matches.decline', matchId));
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
                <Link :href="route('matches.create')">
                    <PrimaryButton>Defier un joueur</PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">
                <!-- Stats -->
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
                    <StatCard title="ELO" :value="stats.elo_rating" color="primary" :subtitle="`Rang #${stats.rank}`" />
                    <StatCard title="Matchs joues" :value="stats.matches_played" color="blue" />
                    <StatCard title="Victoires" :value="stats.matches_won" color="green" />
                    <StatCard title="Defaites" :value="stats.matches_lost" color="red" />
                    <StatCard title="Win Rate" :value="`${stats.win_rate}%`" color="yellow" />
                </div>

                <!-- Pending Challenges -->
                <div v-if="pendingChallenges.length > 0">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Defis en attente</h3>
                    <div class="space-y-3">
                        <div
                            v-for="challenge in pendingChallenges"
                            :key="challenge.id"
                            class="flex items-center justify-between rounded-xl bg-white p-4 shadow-sm border border-yellow-200"
                        >
                            <div class="flex items-center gap-3">
                                <UserAvatar :user="challenge.challenger" size="sm" />
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ challenge.challenger.name }}</p>
                                    <p class="text-xs text-gray-500">vous defie !</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button
                                    @click="acceptChallenge(challenge.id)"
                                    class="rounded-md bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-primary-700 transition"
                                >
                                    Accepter
                                </button>
                                <button
                                    @click="declineChallenge(challenge.id)"
                                    class="rounded-md bg-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-300 transition"
                                >
                                    Refuser
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Matches -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Derniers matchs</h3>
                        <Link :href="route('matches.index')" class="text-sm text-primary-600 hover:text-primary-700">
                            Voir tout
                        </Link>
                    </div>

                    <div v-if="recentMatches.length > 0" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <MatchCard
                            v-for="match in recentMatches"
                            :key="match.id"
                            :match="match"
                            :current-user-id="user.id"
                        />
                    </div>
                    <EmptyState
                        v-else
                        title="Aucun match"
                        description="Commencez par defier un joueur !"
                    >
                        <Link :href="route('matches.create')">
                            <PrimaryButton>Defier un joueur</PrimaryButton>
                        </Link>
                    </EmptyState>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
