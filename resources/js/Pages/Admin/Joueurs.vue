<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Input } from '@/Components/ui/input';
import {
    ArrowLeft,
    Users,
    Search,
    ChevronDown,
    TrendingUp,
    TrendingDown,
    Minus,
    Pencil,
    Trash2,
    Plus,
} from 'lucide-vue-next';

const props = defineProps({
    players: { type: Array, default: () => [] },
    playerCount: { type: Number, default: 0 },
    matchCount: { type: Number, default: 0 },
    averageElo: { type: Number, default: 0 },
    classes: { type: Array, default: () => [] },
    selectedClassId: { type: Number, default: null },
});

const search = ref('');
const sortBy = ref('elo');
const sortOpen = ref(false);

const sortOptions = [
    { value: 'elo', label: 'ELO' },
    { value: 'name', label: 'Nom' },
    { value: 'winRate', label: 'Win rate' },
];

const filteredPlayers = computed(() => {
    let list = [...props.players];

    if (search.value) {
        const q = search.value.toLowerCase();
        list = list.filter(p => p.name.toLowerCase().includes(q));
    }

    if (sortBy.value === 'name') {
        list.sort((a, b) => a.name.localeCompare(b.name));
    } else if (sortBy.value === 'winRate') {
        list.sort((a, b) => b.winRate - a.winRate);
    }

    return list;
});

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(p => p.charAt(0)).join('').toUpperCase();
};

const getWinRateDot = (winRate) => {
    if (winRate >= 70) return 'bg-primary';
    if (winRate >= 50) return 'bg-orange-400';
    return 'bg-red-400';
};
</script>

<template>
    <Head title="Joueurs - Admin" />

    <AdminLayout>
        <div class="p-8">
            <!-- Back link -->
            <Link :href="route('admin.dashboard')" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground transition-colors mb-6">
                <ArrowLeft class="h-4 w-4" />
                Retour au dashboard
            </Link>

            <!-- Header card -->
            <div class="rounded-2xl border border-border p-6 mb-6">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-100">
                            <Users class="h-6 w-6 text-violet-500" />
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="text-xl font-bold text-foreground">Joueurs</h1>
                                <span class="inline-flex h-6 min-w-6 items-center justify-center rounded-full bg-gray-100 px-2 text-xs font-semibold text-foreground">{{ playerCount }}</span>
                            </div>
                            <p class="text-sm text-muted-foreground">Gérer les joueurs, ELO et statistiques</p>
                        </div>
                    </div>
                    <button class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-primary/90">
                        <Plus class="h-4 w-4" />
                        Ajouter un joueur
                    </button>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="rounded-xl border border-border py-4 text-center">
                        <p class="text-xl font-bold text-foreground">{{ averageElo }}</p>
                        <p class="text-xs text-muted-foreground">ELO moyen</p>
                    </div>
                    <div class="rounded-xl border border-border py-4 text-center">
                        <p class="text-xl font-bold text-foreground">{{ matchCount }}</p>
                        <p class="text-xs text-muted-foreground">Matchs joués</p>
                    </div>
                    <div class="rounded-xl border border-border py-4 text-center">
                        <p class="text-xl font-bold text-foreground">{{ playerCount }}</p>
                        <p class="text-xs text-muted-foreground">Joueurs actifs</p>
                    </div>
                </div>
            </div>

            <!-- Search + Sort -->
            <div class="flex items-center gap-4 mb-4">
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                    <Input
                        v-model="search"
                        type="text"
                        placeholder="Rechercher un joueur..."
                        class="pl-9 w-full text-sm placeholder:text-gray-400 shadow-none focus-visible:ring-1 focus-visible:ring-primary"
                    />
                </div>
                <div class="relative">
                    <button
                        class="inline-flex h-9 items-center gap-2 rounded-md border border-input bg-white px-4 text-sm text-foreground transition-colors hover:bg-gray-50 cursor-pointer"
                        @click="sortOpen = !sortOpen"
                    >
                        Trier : {{ sortOptions.find(o => o.value === sortBy)?.label }}
                        <ChevronDown class="h-4 w-4 text-muted-foreground" />
                    </button>
                    <div v-if="sortOpen" class="absolute right-0 top-full z-50 mt-1 min-w-[150px] rounded-xl border border-border bg-white py-1 shadow-lg">
                        <button
                            v-for="opt in sortOptions"
                            :key="opt.value"
                            class="flex w-full px-4 py-2 text-left text-sm transition-colors hover:bg-gray-50 cursor-pointer"
                            :class="sortBy === opt.value && 'text-primary font-medium'"
                            @click="sortBy = opt.value; sortOpen = false"
                        >
                            {{ opt.label }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Player list -->
            <div class="rounded-2xl border border-border divide-y">
                <div
                    v-for="player in filteredPlayers"
                    :key="player.rank"
                    class="flex items-center gap-4 px-5 py-4"
                >
                    <!-- Rank -->
                    <span class="w-6 text-center text-sm font-medium text-muted-foreground">{{ player.rank }}</span>

                    <!-- Avatar -->
                    <Avatar class="h-11 w-11 shrink-0">
                        <AvatarImage v-if="player.avatar" :src="player.avatar" />
                        <AvatarFallback class="text-xs">{{ getInitials(player.name) }}</AvatarFallback>
                    </Avatar>

                    <!-- Info -->
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-foreground">{{ player.name }}</span>
                            <template v-if="player.trend > 0">
                                <TrendingUp class="h-3.5 w-3.5 text-primary" />
                                <span class="text-xs font-medium text-primary">+{{ player.trend }}</span>
                            </template>
                            <template v-else-if="player.trend < 0">
                                <TrendingDown class="h-3.5 w-3.5 text-destructive" />
                                <span class="text-xs font-medium text-destructive">{{ player.trend }}</span>
                            </template>
                            <template v-else>
                                <Minus class="h-3.5 w-3.5 text-muted-foreground" />
                                <span class="text-xs text-muted-foreground">0</span>
                            </template>
                        </div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-xs">
                                <span class="font-medium text-primary">{{ player.wins }}V</span>
                                <span class="text-muted-foreground"> · </span>
                                <span class="font-medium text-destructive">{{ player.losses }}D</span>
                            </span>
                            <span class="h-2 w-2 rounded-full" :class="getWinRateDot(player.winRate)" />
                            <span class="text-xs text-muted-foreground">{{ player.winRate }}%</span>
                        </div>
                    </div>

                    <!-- ELO -->
                    <div class="text-right shrink-0">
                        <p class="text-lg font-bold text-foreground">{{ player.elo }}</p>
                        <p class="text-xs text-muted-foreground">ELO</p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 shrink-0">
                        <button class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-lg transition-colors hover:bg-gray-100">
                            <Pencil class="h-4 w-4 text-muted-foreground" />
                        </button>
                        <button class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-lg transition-colors hover:bg-red-50">
                            <Trash2 class="h-4 w-4 text-destructive" />
                        </button>
                    </div>
                </div>

                <!-- Empty state -->
                <div v-if="filteredPlayers.length === 0" class="py-12 text-center">
                    <p class="text-sm text-muted-foreground">Aucun joueur trouvé.</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
