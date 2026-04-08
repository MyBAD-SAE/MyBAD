<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AdminClassPicker from '@/Components/admin/AdminClassPicker.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Crown, Layers, Lightbulb } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps({
    classes: { type: Array, default: () => [] },
    selectedClassId: { type: Number, default: null },
    playerCount: { type: Number, default: 0 },
    rankingPlayers: { type: Array, default: () => [] },
    enableRankingGroups: { type: Boolean, default: false },
    enableEloHandicap: { type: Boolean, default: false },
    groupSize: { type: Number, default: 8 },
});

const livePlayerCount = ref(props.playerCount);
const liveRankingPlayers = ref(props.rankingPlayers);
const liveEnableRankingGroups = ref(props.enableRankingGroups);
const liveEnableEloHandicap = ref(props.enableEloHandicap);
const liveGroupSize = ref(props.groupSize);

let pollInterval = null;

const fetchRanking = async () => {
    try {
        const { data } = await axios.get(route('admin.ranking.data'));
        livePlayerCount.value = data.playerCount;
        liveRankingPlayers.value = data.rankingPlayers;
        liveEnableRankingGroups.value = data.enableRankingGroups;
        liveEnableEloHandicap.value = data.enableEloHandicap;
        liveGroupSize.value = data.groupSize;
    } catch (e) {
        // silently ignore polling errors
    }
};

onMounted(() => {
    pollInterval = setInterval(fetchRanking, 10000);
});

onUnmounted(() => {
    clearInterval(pollInterval);
});

const podium = computed(() => {
    const len = liveRankingPlayers.value.length;
    if (len < 2) return [];
    const top = liveRankingPlayers.value.slice(0, 3);
    if (len === 2) return [top[1], top[0], null];
    return [top[1], top[0], top[2]];
});

const restPlayers = computed(() => liveRankingPlayers.value.slice(3));

const columns = computed(() => {
    const players = restPlayers.value;
    const colCount = 4;
    const perCol = Math.ceil(players.length / colCount);
    const cols = [];
    for (let i = 0; i < colCount; i++) {
        const chunk = players.slice(i * perCol, (i + 1) * perCol);
        if (chunk.length > 0) cols.push(chunk);
    }
    return cols;
});

const getRankColor = (rank) => {
    if (rank <= 10) return { bg: 'bg-emerald-50', text: 'text-emerald-600' };
    if (rank <= 20) return { bg: 'bg-sky-50', text: 'text-sky-600' };
    if (rank <= 30) return { bg: 'bg-amber-50', text: 'text-amber-600' };
    if (rank <= 40) return { bg: 'bg-orange-50', text: 'text-orange-600' };
    return { bg: 'bg-rose-50', text: 'text-rose-600' };
};

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(p => p.charAt(0)).join('').toUpperCase();
};
</script>

<template>
    <Head title="Classement - Admin" />

    <div class="min-h-screen bg-gray-50 p-2">
        <div class="rounded-2xl border border-gray-200 bg-white p-6">
            <!-- Header -->
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-foreground">Classement</h1>
                    <div class="flex items-center gap-1.5 mt-1">
                        <span class="h-2 w-2 rounded-full bg-emerald-400" />
                        <span class="text-sm text-muted-foreground">{{ livePlayerCount }} joueurs</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div v-if="liveEnableRankingGroups" class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700">
                        <Layers class="h-4 w-4" />
                        Groupes de {{ liveGroupSize }}
                    </div>
                    <div v-if="liveEnableEloHandicap" class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-4 py-2 text-sm font-medium text-amber-700">
                        <Lightbulb class="h-4 w-4" />
                        Handicap actif
                    </div>
                </div>
            </div>

            <!-- Podium -->
            <div v-if="podium.length > 0" class="flex items-end justify-center gap-8 mb-8">
                    <!-- 2nd place -->
                    <div class="flex flex-col items-center">
                        <div class="relative">
                            <Avatar class="h-14 w-14 ring-[3px] ring-gray-300">
                                <AvatarImage v-if="podium[0].avatar" :src="podium[0].avatar" />
                                <AvatarFallback class="text-xs">{{ getInitials(podium[0].name) }}</AvatarFallback>
                            </Avatar>
                            <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 flex h-5 w-5 items-center justify-center rounded-full bg-gray-400 text-[10px] font-bold text-white shadow">
                                2
                            </div>
                        </div>
                        <p class="mt-3 text-sm font-semibold text-foreground">{{ podium[0].name }}</p>
                        <div class="mt-2 h-12 w-36 rounded-t-xl bg-gray-200 flex items-center justify-center">
                            <span class="text-sm font-bold text-gray-500">{{ podium[0].elo }}</span>
                        </div>
                    </div>

                    <!-- 1st place -->
                    <div class="flex flex-col items-center">
                        <div class="relative">
                            <Avatar class="h-16 w-16 ring-[3px] ring-yellow-400">
                                <AvatarImage v-if="podium[1].avatar" :src="podium[1].avatar" />
                                <AvatarFallback class="text-sm">{{ getInitials(podium[1].name) }}</AvatarFallback>
                            </Avatar>
                            <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 flex h-5 w-5 items-center justify-center rounded-full bg-yellow-400 text-[10px] font-bold text-white shadow">
                                <Crown class="h-3 w-3" />
                            </div>
                        </div>
                        <p class="mt-3 text-sm font-semibold text-foreground">{{ podium[1].name }}</p>
                        <div class="mt-2 h-16 w-36 rounded-t-xl bg-yellow-100 flex items-center justify-center">
                            <span class="text-sm font-bold text-yellow-600">{{ podium[1].elo }}</span>
                        </div>
                    </div>

                    <!-- 3rd place -->
                    <div class="flex flex-col items-center">
                        <div class="relative">
                            <Avatar class="h-14 w-14 ring-[3px] ring-orange-300">
                                <AvatarImage v-if="podium[2]?.avatar" :src="podium[2].avatar" />
                                <AvatarFallback class="text-xs">{{ getInitials(podium[2]?.name) }}</AvatarFallback>
                            </Avatar>
                            <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 flex h-5 w-5 items-center justify-center rounded-full bg-orange-400 text-[10px] font-bold text-white shadow">
                                3
                            </div>
                        </div>
                        <p class="mt-3 text-sm font-semibold text-foreground">{{ podium[2]?.name }}</p>
                        <div class="mt-2 h-8 w-36 rounded-t-xl bg-orange-100 flex items-center justify-center">
                            <span class="text-sm font-bold text-orange-500">{{ podium[2]?.elo }}</span>
                        </div>
                    </div>
            </div>

            <!-- Ranking grid - always 4 columns -->
            <div v-if="columns.length > 0" class="grid grid-cols-4 gap-4">
                <div
                    v-for="(column, colIndex) in columns"
                    :key="colIndex"
                    class="rounded-2xl border border-border p-3"
                >
                    <div
                        v-for="player in column"
                        :key="player.rank"
                        class="flex items-center gap-3 px-3 py-3 border-b border-gray-100 last:border-b-0"
                    >
                        <!-- Rank -->
                        <span
                            class="w-6 text-center text-sm font-bold"
                            :class="getRankColor(player.rank).text"
                        >{{ player.rank }}</span>

                        <!-- Avatar -->
                        <Avatar class="h-8 w-8 shrink-0">
                            <AvatarImage v-if="player.avatar" :src="player.avatar" />
                            <AvatarFallback class="text-[10px]">{{ getInitials(player.name) }}</AvatarFallback>
                        </Avatar>

                        <!-- Name -->
                        <span class="flex-1 truncate text-sm font-medium text-foreground">{{ player.name }}</span>

                        <!-- ELO -->
                        <span class="shrink-0 text-sm font-bold text-foreground">{{ player.elo }}</span>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-if="liveRankingPlayers.length === 0" class="py-20 text-center">
                <p class="text-sm text-muted-foreground">Aucun joueur dans le classement pour le moment.</p>
            </div>
        </div>
    </div>
</template>
