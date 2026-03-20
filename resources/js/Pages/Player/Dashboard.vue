<script setup>
import { Head } from '@inertiajs/vue3';
import PlayerLayout from '@/Layouts/PlayerLayout.vue';

import DashboardHeader from '@/Components/dashboard/DashboardHeader.vue';
import SuggestionCard from '@/Components/dashboard/SuggestionCard.vue';
import EloCard from '@/Components/dashboard/EloCard.vue';
import MatchStatsCard from '@/Components/dashboard/MatchStatsCard.vue';
import EvolutionCard from '@/Components/dashboard/EvolutionCard.vue';
import GlobalActivityCard from '@/Components/dashboard/GlobalActivityCard.vue';
import RankingWidget from '@/Components/dashboard/RankingWidget.vue';
import RecentMatchesWidget from '@/Components/dashboard/RecentMatchesWidget.vue';
import BottomNavBar from '@/Components/BottomNavBar.vue';
import { Card, CardContent } from '@/Components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Clock } from 'lucide-vue-next';

const props = defineProps({
    participant : Object,
    playerCode: String,
    firstName: String,
    avatarUrl: String,
    eloDiff : Number,
    eloHistory : Array,
    matchStats : Object,
    totalMatches: Number,
    winStreak: Number,
    rankingPlayers: Array,
})

const formattedCode = (code) => {
    if (!code) return '';
    const padded = code.toString().padStart(6, '0');
    return padded.slice(0, 3) + ' ' + padded.slice(3);
}
</script>

<template>
    <Head title="Dashboard" />

    <PlayerLayout>
        <div class="pb-20">
            <div class="space-y-6 p-5">

                <!-- Pas inscrit dans un cours -->
                <template v-if="!participant">
                    <div class="flex items-start justify-between">
                        <h1 class="text-2xl font-bold text-foreground">Bonjour {{ firstName }} 👋</h1>
                        <Avatar class="h-10 w-10">
                            <AvatarImage v-if="avatarUrl" :src="avatarUrl" alt="Avatar" />
                            <AvatarFallback>{{ firstName?.charAt(0) }}</AvatarFallback>
                        </Avatar>
                    </div>

                    <Card class="border-0 shadow-sm">
                        <CardContent class="p-6">
                            <h2 class="text-xl font-bold text-foreground">Rejoindre un cours</h2>
                            <p class="mt-1 text-sm text-muted-foreground">
                                Communiquez ce code à votre professeur pour être ajouté à une séance.
                            </p>

                            <div class="mt-5 rounded-2xl bg-emerald-50 py-6 text-center">
                                <span class="text-4xl font-bold tracking-[0.3em] text-emerald-500">
                                    {{ formattedCode(playerCode) }}
                                </span>
                            </div>

                            <div class="mt-5 flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-muted">
                                    <Clock class="h-4 w-4 text-muted-foreground" />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-foreground">En attente d'un cours</p>
                                    <p class="text-sm text-muted-foreground">
                                        Vos statistiques et votre classement apparaîtront une fois ajouté à une séance.
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </template>

                <!-- Inscrit dans un cours -->
                <template v-else>
                    <!-- Header -->
                    <DashboardHeader :first-name="participant?.participantable?.user?.first_name ?? ''" :avatar-url="participant?.participantable?.user?.profile_picture" />

                    <!-- Suggestion -->
                    <SuggestionCard />

                    <!-- Activité des 4 dernières séances -->
                    <div>
                        <h3 class="text-lg font-bold text-foreground">4 dernières séances</h3>

                        <div class="mt-3">
                            <EloCard :elo="participant?.elo_rating ?? 0" :elo-diff="eloDiff" :history="eloHistory" :has-matches="totalMatches > 0" />
                        </div>

                        <div v-if="matchStats.total > 0" class="mt-3 grid grid-cols-2 gap-3">
                            <MatchStatsCard :total="matchStats.total" :wins="matchStats.wins" :losses="matchStats.losses" :sessions="matchStats.sessions" />
                            <EvolutionCard :wins="matchStats.wins" :losses="matchStats.losses" :total="matchStats.total" />
                        </div>
                    </div>

                    <!-- Activité globale -->
                    <GlobalActivityCard :win-streak="winStreak" :total-matches="totalMatches" />

                    <!-- Classement -->
                    <RankingWidget :players="rankingPlayers" />

                    <!-- Derniers matchs -->
                    <RecentMatchesWidget />
                </template>
            </div>
        </div>

        <!-- Bottom nav -->
        <BottomNavBar active="dashboard" />
    </PlayerLayout>
</template>