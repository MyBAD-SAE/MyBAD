<script setup>
import { Head, usePage } from '@inertiajs/vue3';
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

const props = defineProps({
    participant : Object,
    eloDiff : Number,
    eloHistory : Array,
    matchStats : Object,
    winStreak: Number,
})

console.log('Participant', props.participant)
</script>

<template>
    <Head title="Dashboard" />

    <PlayerLayout>
        <div class="pb-20">
            <div class="space-y-6 p-5">
                <!-- Header -->
                <DashboardHeader :first-name="participant.participantable.user.first_name" :avatar-url="participant.participantable.user.profile_picture" />

                <!-- Suggestion -->
                <SuggestionCard />

                <!-- Activité mensuelle -->
                <div>
                    <h3 class="text-lg font-bold text-foreground">Activité mensuelle</h3>
                    <p class="text-sm text-muted-foreground">Depuis le début</p>

                    <div class="mt-3">
                        <EloCard :elo="participant.elo_rating" :elo-diff="eloDiff" :history="eloHistory" />
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-3">
                        <MatchStatsCard :total="matchStats.total" :wins="matchStats.wins" :losses="matchStats.losses" :sessions="matchStats.sessions" />
                        <EvolutionCard :wins="matchStats.wins" :losses="matchStats.losses" :total="matchStats.total" />
                    </div>
                </div>

                <!-- Activité globale -->
                <GlobalActivityCard :win-streak="winStreak" :total-matches="matchStats.totalMatches" />

                <!-- Classement -->
                <RankingWidget />

                <!-- Derniers matchs -->
                <RecentMatchesWidget />
            </div>
        </div>

        <!-- Bottom nav -->
        <BottomNavBar active="dashboard" />
    </PlayerLayout>
</template>
