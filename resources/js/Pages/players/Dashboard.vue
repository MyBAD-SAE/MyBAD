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
import BottomNavBar from '@/Components/dashboard/BottomNavBar.vue';
</script>

<template>
    <Head title="Dashboard" />

    <PlayerLayout>
        <div class="pb-20">
            <div class="space-y-6 p-5">
                <!-- Header -->
                <DashboardHeader first-name="Lucas" />

                <!-- Suggestion -->
                <SuggestionCard />

                <!-- Activité mensuelle -->
                <div>
                    <h3 class="text-lg font-bold text-foreground">Activité mensuelle</h3>
                    <p class="text-sm text-muted-foreground">Depuis le début</p>

                    <div class="mt-3">
                        <EloCard :elo="100.0" :elo-diff="22" />
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-3">
                        <MatchStatsCard :total="36" :wins="25" :losses="12" />
                        <EvolutionCard :wins="17" :losses="5" :total="22" />
                    </div>
                </div>

                <!-- Activité globale -->
                <GlobalActivityCard :win-streak="4" :total-matches="50" />

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
