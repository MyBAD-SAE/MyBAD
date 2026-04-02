<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import {
    AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent,
    AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle,
} from '@/Components/ui/alert-dialog';
import {
    ArrowLeft,
    Medal,
    Calendar,
    Trophy,
    TrendingUp,
    TrendingDown,
    Crown,
    X,
} from 'lucide-vue-next';

const props = defineProps({
    session: Object,
    rankingPlayers: { type: Array, default: () => [] },
    playerCount: { type: Number, default: 0 },
    recentMatches: { type: Array, default: () => [] },
});

const showCloseDialog = ref(false);

function closeSession() {
    router.post(route('admin.session.close'));
}

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(p => p.charAt(0)).join('').toUpperCase();
};

const podiumOrder = [1, 0, 2]; // 2e gauche, 1er centre, 3e droite

const getRankBadgeClass = (rank) => {
    if (rank === 1) return 'bg-yellow-100 text-yellow-700';
    if (rank === 2) return 'bg-gray-100 text-gray-600';
    if (rank === 3) return 'bg-orange-100 text-orange-700';
    return 'bg-gray-100 text-muted-foreground';
};

const getWinRateColor = (winRate) => {
    if (winRate >= 70) return 'bg-primary';
    if (winRate >= 50) return 'bg-orange-400';
    return 'bg-red-400';
};
</script>

<template>
    <Head title="Séance en cours" />

    <AdminLayout>
        <div class="p-8 space-y-6">
            <!-- Retour -->
            <Link :href="route('admin.dashboard')" class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground transition-colors">
                <ArrowLeft class="h-4 w-4" />
                Retour au dashboard
            </Link>

            <!-- Session header -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-yellow-100">
                        <Medal class="h-6 w-6 text-yellow-600" />
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-xl font-bold text-foreground">{{ session.session_name }}</h1>
                            <span v-if="session.is_active" class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-600">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" />
                                En cours
                            </span>
                        </div>
                        <p class="mt-1 flex items-center gap-1.5 text-sm text-muted-foreground">
                            <Calendar class="h-3.5 w-3.5" />
                            {{ session.formatted_date }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Content grid -->
            <div class="grid grid-cols-5 gap-6">
                <!-- Classement -->
                <div class="col-span-3 rounded-2xl border border-gray-200 bg-white p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-foreground">Classement</h2>
                            <p class="mt-1 text-sm text-muted-foreground">{{ playerCount }} joueurs · temps réel</p>
                        </div>
                        <a
                            href="/admin/classement"
                            class="text-sm font-medium text-primary"
                        >
                            Voir en plein écran &gt;
                        </a>
                    </div>

                    <!-- Podium -->
                    <div v-if="rankingPlayers.length >= 3" class="mt-8 mb-8 flex items-end justify-center gap-4">
                        <div v-for="idx in podiumOrder" :key="idx" class="flex flex-col items-center gap-2"
                            :class="idx === 0 ? 'order-2' : idx === 1 ? 'order-1' : 'order-3'">
                            <div class="relative">
                                <Crown v-if="idx === 0" class="absolute -top-5 left-1/2 z-10 h-5 w-5 -translate-x-1/2 text-yellow-400" />
                                <Avatar :class="[idx === 0 ? 'h-16 w-16 ring-3 ring-yellow-400' : 'h-12 w-12 ring-3', idx === 1 ? 'ring-gray-300' : '', idx === 2 ? 'ring-orange-300' : '']">
                                    <AvatarImage v-if="rankingPlayers[idx]?.avatar" :src="rankingPlayers[idx].avatar" />
                                    <AvatarFallback class="text-xs">{{ getInitials(rankingPlayers[idx]?.name || '') }}</AvatarFallback>
                                </Avatar>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-semibold text-foreground">{{ rankingPlayers[idx]?.name.split(' ')[0] }}</p>
                                <p class="text-xs text-muted-foreground">{{ rankingPlayers[idx]?.elo }} pts</p>
                            </div>
                            <div
                                class="w-30 rounded-t-lg flex items-end justify-center pb-2"
                                :class="idx === 0 ? 'h-20 bg-yellow-100' : idx === 1 ? 'h-14 bg-gray-100' : 'h-10 bg-orange-100'"
                            >
                                <span class="text-sm font-bold" :class="idx === 0 ? 'text-yellow-700' : idx === 1 ? 'text-gray-600' : 'text-orange-700'">
                                    {{ idx === 0 ? '1er' : `${idx + 1}e` }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Player list -->
                    <div class="divide-y">
                        <div v-for="player in rankingPlayers.slice(3)" :key="player.rank" class="flex items-center gap-4 py-3.5">
                            <!-- Rank badge -->
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-semibold" :class="getRankBadgeClass(player.rank)">
                                <Trophy v-if="player.rank === 1" class="h-4 w-4" />
                                <span v-else>{{ player.rank }}</span>
                            </div>

                            <!-- Avatar + name -->
                            <Avatar class="h-10 w-10 shrink-0">
                                <AvatarImage v-if="player.avatar" :src="player.avatar" />
                                <AvatarFallback class="text-xs">{{ getInitials(player.name) }}</AvatarFallback>
                            </Avatar>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-foreground">{{ player.name }}</p>
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="font-medium text-primary">{{ player.wins }}V</span>
                                    <span class="text-muted-foreground">·</span>
                                    <span class="font-medium text-destructive">{{ player.losses }}D</span>
                                    <template v-if="player.trend !== 0">
                                        <TrendingUp v-if="player.trend > 0" class="ml-1 h-3 w-3 text-primary" />
                                        <TrendingDown v-else class="ml-1 h-3 w-3 text-destructive" />
                                        <span :class="player.trend > 0 ? 'text-primary' : 'text-destructive'">
                                            {{ player.trend > 0 ? '+' : '' }}{{ player.trend }}
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <!-- ELO + Win rate -->
                            <div class="text-right">
                                <p class="text-sm font-bold text-foreground">{{ player.elo }}</p>
                                <div class="mt-1 flex items-center gap-2">
                                    <div class="h-1.5 w-16 rounded-full bg-gray-100">
                                        <div
                                            class="h-full rounded-full transition-all"
                                            :class="getWinRateColor(player.winRate)"
                                            :style="{ width: player.winRate + '%' }"
                                        />
                                    </div>
                                    <span class="text-xs text-muted-foreground">{{ player.winRate }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="rankingPlayers.length === 0" class="py-12 text-center">
                        <p class="text-sm text-muted-foreground">Aucun joueur dans le classement.</p>
                    </div>
                </div>

                <!-- Right column -->
                <div class="col-span-2 space-y-6">
                    <!-- Derniers matchs -->
                    <div class="rounded-2xl border border-gray-200 bg-white p-6">
                        <h2 class="text-lg font-bold text-foreground">Derniers matchs</h2>

                        <div class="mt-4 space-y-0 divide-y">
                            <div v-for="match in recentMatches" :key="match.id" class="flex items-center justify-between py-3">
                                <div class="flex items-center gap-2 text-sm">
                                    <span :class="match.winnerIndex === 0 ? 'font-semibold text-primary' : 'text-foreground'">
                                        {{ match.player1.name }}
                                    </span>
                                    <span class="font-bold text-foreground">{{ match.player1.score }} – {{ match.player2.score }}</span>
                                    <span :class="match.winnerIndex === 1 ? 'font-semibold text-primary' : 'text-foreground'">
                                        {{ match.player2.name }}
                                    </span>
                                </div>
                                <span class="text-xs text-muted-foreground">{{ match.timeAgo }}</span>
                            </div>
                        </div>

                        <div v-if="recentMatches.length === 0" class="py-8 text-center">
                            <p class="text-sm text-muted-foreground">Aucun match pour le moment.</p>
                        </div>
                    </div>

                    <!-- Clôturer la séance -->
                    <button
                        @click="showCloseDialog = true"
                        class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-2xl border border-red-200 bg-white py-4 text-sm font-medium text-red-500 transition-colors hover:bg-red-50"
                    >
                        <X class="h-4 w-4" />
                        Clôturer la séance
                    </button>
                </div>
            </div>
        </div>

        <!-- Close session dialog -->
        <AlertDialog :open="showCloseDialog" @update:open="showCloseDialog = $event">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Clôturer la séance ?</AlertDialogTitle>
                    <AlertDialogDescription>
                        Cette action mettra fin à la séance en cours. Les joueurs ne pourront plus déclarer de matchs.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Annuler</AlertDialogCancel>
                    <AlertDialogAction @click="closeSession">Clôturer</AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AdminLayout>
</template>
