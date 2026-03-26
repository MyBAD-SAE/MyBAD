<script setup>
import { Head, Link } from '@inertiajs/vue3';
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
import { Clock, Copy, Check, UserPlus, PartyPopper } from 'lucide-vue-next';
import { ref } from 'vue';

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
    classes: { type: Array, default: () => [] },
    selectedClassId: { type: Number, default: null },
})

const formattedCode = (code) => {
    if (!code) return '';
    const padded = code.toString().padStart(6, '0');
    return padded.slice(0, 3) + ' ' + padded.slice(3);
}

const copied = ref(false);
function copyCode() {
    if (!props.playerCode) return;
    navigator.clipboard.writeText(props.playerCode.toString());
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
}
</script>

<template>
    <Head title="Dashboard" />

    <PlayerLayout>
        <div class="pb-20">
            <div class="space-y-6 p-5">

                <!-- Pas inscrit dans un cours -->
                <template v-if="!participant">
                    <div class="flex items-center justify-between">
                        <h1 class="text-lg font-semibold text-foreground">Bonjour {{ firstName }} <PartyPopper class="inline h-5 w-5 -translate-y-0.5 text-amber-400" /></h1>
                        <Link :href="route('player.account.index')">
                            <Avatar class="h-8 w-8">
                                <AvatarImage v-if="avatarUrl" :src="avatarUrl" alt="Avatar" />
                                <AvatarFallback class="text-xs">{{ firstName?.charAt(0) }}</AvatarFallback>
                            </Avatar>
                        </Link>
                    </div>

                    <Card class="shadow-none border-border/40">
                        <CardContent class="px-4 py-5">
                            <!-- Illustration + message d'accueil -->
                            <div class="flex flex-col items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10">
                                    <UserPlus class="h-4.5 w-4.5 text-primary" />
                                </div>
                                <h2 class="mt-2.5 text-center text-sm font-semibold text-foreground">Rejoindre un cours</h2>
                                <p class="mt-0.5 text-center text-xs leading-relaxed text-muted-foreground">
                                    Communiquez ce code à votre professeur<br />pour être ajouté à un cours.
                                </p>
                            </div>

                            <!-- Code joueur -->
                            <button
                                class="mt-4 flex w-full items-center justify-between rounded-xl border border-primary/20 bg-primary/5 px-4 py-3 transition-colors active:bg-primary/10"
                                @click="copyCode"
                            >
                                <div class="flex flex-col items-start gap-0.5">
                                    <span class="text-[9px] font-semibold uppercase tracking-widest text-primary/60">Mon code joueur</span>
                                    <span class="font-mono text-lg font-bold tracking-[0.2em] text-primary">
                                        {{ formattedCode(playerCode) }}
                                    </span>
                                </div>
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10">
                                    <Check v-if="copied" class="h-3.5 w-3.5 text-primary" />
                                    <Copy v-else class="h-3.5 w-3.5 text-primary/60" />
                                </div>
                            </button>
                            <p class="mt-1 text-center text-[10px] text-muted-foreground/50">Appuyez pour copier le code</p>

                            <!-- Statut en attente -->
                            <div class="mt-4 flex items-center gap-2.5 rounded-xl bg-muted/40 px-3 py-2.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-50">
                                    <Clock class="h-3.5 w-3.5 text-amber-400" />
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-foreground">En attente d'un cours</p>
                                    <p class="text-[11px] leading-relaxed text-muted-foreground">
                                        Vos statistiques apparaîtront une fois ajouté à un cours.
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </template>

                <!-- Inscrit dans un cours -->
                <template v-else>
                    <!-- Header -->
                    <DashboardHeader
                        :first-name="participant?.participantable?.user?.first_name ?? ''"
                        :avatar-url="participant?.participantable?.user?.profile_picture"
                        :classes="classes"
                        :selected-class-id="selectedClassId"
                    />

                    <!-- Suggestion -->
                    <SuggestionCard />

                    <!-- Activité des 4 dernières séances -->
                    <div>
                        <h3 class="text-lg font-semibold text-foreground">4 dernières séances</h3>

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
