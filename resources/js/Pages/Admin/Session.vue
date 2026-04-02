<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import {
    AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent,
    AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle,
} from '@/Components/ui/alert-dialog';
import {
    ArrowLeft,
    Medal,
    Link2,
    ExternalLink,
    Copy,
    Calendar,
    Trophy,
    TrendingUp,
    TrendingDown,
    Minus,
    X,
} from 'lucide-vue-next';

const props = defineProps({
    session: Object,
    publicLink: { type: String, default: null },
    rankingPlayers: { type: Array, default: () => [] },
    playerCount: { type: Number, default: 0 },
    recentMatches: { type: Array, default: () => [] },
});

const showCloseDialog = ref(false);
const linkCopied = ref(false);

const publicUrl = props.publicLink
    ? `${window.location.origin}/live/${props.publicLink}`
    : null;

function copyLink() {
    if (!publicUrl) return;
    navigator.clipboard.writeText(publicUrl);
    linkCopied.value = true;
    setTimeout(() => { linkCopied.value = false; }, 2000);
}

function closeSession() {
    router.post(route('admin.session.close'));
}

// Auto-refresh every 10 seconds
let refreshInterval = null;
onMounted(() => {
    refreshInterval = setInterval(() => {
        router.reload({ only: ['rankingPlayers', 'recentMatches', 'playerCount'] });
    }, 10000);
});
onUnmounted(() => {
    clearInterval(refreshInterval);
});

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(p => p.charAt(0)).join('').toUpperCase();
};

const podiumPlayers = computed(() => props.rankingPlayers.slice(0, 3));
const podiumOrder = computed(() => [
    podiumPlayers.value[1] ?? null, // 2e — gauche
    podiumPlayers.value[0] ?? null, // 1er — centre
    podiumPlayers.value[2] ?? null, // 3e — droite
]);

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

const getPodiumHeight = (rank) => {
    if (rank === 1) return 'h-20';
    if (rank === 2) return 'h-14';
    return 'h-10';
};

const getPodiumBg = (rank) => {
    if (rank === 1) return 'bg-yellow-100';
    if (rank === 2) return 'bg-gray-100';
    return 'bg-orange-100';
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
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-yellow-100">
                            <Medal class="h-6 w-6 text-yellow-600" />
                        </div>
                        <div>
                            <div class="flex items-center gap-3">
                                <h1 class="text-xl font-bold text-foreground">{{ session.dayName }}</h1>
                                <span v-if="session.isActive" class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-600">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" />
                                    En cours
                                </span>
                            </div>
                            <p class="mt-1 flex items-center gap-1.5 text-sm text-muted-foreground">
                                <Calendar class="h-3.5 w-3.5" />
                                {{ session.fullDate }}
                            </p>
                        </div>
                    </div>

                    <div v-if="publicUrl" class="flex items-center gap-2">
                        <a
                            :href="publicUrl"
                            target="_blank"
                            class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-primary/90"
                        >
                            <ExternalLink class="h-4 w-4" />
                            Ouvrir live
                        </a>
                        <button
                            @click="copyLink"
                            class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium transition-colors hover:bg-gray-50"
                            :class="linkCopied ? 'text-primary' : 'text-foreground'"
                        >
                            <Copy class="h-4 w-4" />
                            {{ linkCopied ? 'Copié !' : 'Copier lien' }}
                        </button>
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
                            :href="route('admin.ranking')"
                            target="_blank"
                            class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-primary/80 transition-colors"
                        >
                            Voir en plein écran
                            <ExternalLink class="h-3.5 w-3.5" />
                        </a>
                    </div>

                    <!-- Podium -->
                    <div v-if="podiumPlayers.length >= 3" class="mt-8 mb-8 flex items-end justify-center gap-4">
                        <div v-for="player in podiumOrder" :key="player?.rank" class="flex flex-col items-center gap-2">
                            <div class="relative">
                                <Avatar class="h-16 w-16 border-2" :class="player?.rank === 1 ? 'border-yellow-400' : 'border-gray-200'">
                                    <AvatarImage v-if="player?.avatar" :src="player.avatar" />
                                    <AvatarFallback class="text-sm">{{ getInitials(player?.name) }}</AvatarFallback>
                                </Avatar>
                                <span v-if="player?.rank === 1" class="absolute -top-3 left-1/2 -translate-x-1/2 text-lg">👑</span>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-semibold text-foreground">{{ player?.name?.split(' ')[0] }}</p>
                                <p class="text-xs text-muted-foreground">{{ player?.elo }}</p>
                            </div>
                            <div
                                class="w-20 rounded-t-lg flex items-end justify-center pb-2"
                                :class="[getPodiumHeight(player?.rank), getPodiumBg(player?.rank)]"
                            >
                                <span class="text-sm font-bold" :class="player?.rank === 1 ? 'text-yellow-700' : player?.rank === 2 ? 'text-gray-600' : 'text-orange-700'">
                                    {{ player?.rank === 1 ? '1er' : player?.rank + 'e' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Player list -->
                    <div class="space-y-0 divide-y">
                        <div v-for="player in rankingPlayers" :key="player.rank" class="flex items-center gap-4 py-3.5">
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
