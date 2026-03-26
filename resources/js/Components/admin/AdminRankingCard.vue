<script setup>
import { ref } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Tabs, TabsList, TabsTrigger } from '@/Components/ui/tabs';
import { TrendingUp, TrendingDown, Minus, Trophy } from 'lucide-vue-next';

defineProps({
    players: { type: Array, default: () => [] },
    playerCount: { type: Number, default: 0 },
});

const period = ref('30j');

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(p => p.charAt(0)).join('').toUpperCase();
};

const getRankBadgeClass = (rank) => {
    if (rank === 1) return 'bg-yellow-100 text-yellow-700';
    if (rank === 2) return 'bg-gray-100 text-gray-600';
    if (rank === 3) return 'bg-orange-100 text-orange-700';
    return 'bg-transparent text-muted-foreground';
};

const getWinRateColor = (winRate) => {
    if (winRate >= 70) return 'bg-primary';
    if (winRate >= 50) return 'bg-orange-400';
    return 'bg-red-400';
};
</script>

<template>
    <div class="rounded-2xl border bg-white p-6">
        <!-- Header -->
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-xl font-bold text-foreground">Classement</h2>
                <p class="mt-1 text-sm text-muted-foreground">{{ playerCount }} joueurs · saison en cours</p>
            </div>
            <Tabs v-model="period">
                <TabsList class="h-9">
                    <TabsTrigger value="30j" class="px-3 text-xs">30j</TabsTrigger>
                    <TabsTrigger value="tout" class="px-3 text-xs">Tout</TabsTrigger>
                </TabsList>
            </Tabs>
        </div>

        <!-- Table -->
        <div class="mt-6 overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b text-xs font-medium uppercase tracking-wider text-muted-foreground">
                        <th class="pb-3 text-left w-12">#</th>
                        <th class="pb-3 text-left">Joueur</th>
                        <th class="pb-3 text-left">ELO</th>
                        <th class="pb-3 text-left">V / D</th>
                        <th class="pb-3 text-left">Tendance</th>
                        <th class="pb-3 text-left w-40">Win rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="player in players" :key="player.rank" class="group">
                        <!-- Rank -->
                        <td class="py-4">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg text-sm font-semibold" :class="getRankBadgeClass(player.rank)">
                                <Trophy v-if="player.rank === 1" class="h-4 w-4" />
                                <span v-else>{{ player.rank }}</span>
                            </div>
                        </td>

                        <!-- Player -->
                        <td class="py-4">
                            <div class="flex items-center gap-3">
                                <Avatar class="h-9 w-9 shrink-0">
                                    <AvatarImage v-if="player.avatar" :src="player.avatar" />
                                    <AvatarFallback class="text-xs">{{ getInitials(player.name) }}</AvatarFallback>
                                </Avatar>
                                <span class="text-sm font-semibold text-foreground">{{ player.name }}</span>
                            </div>
                        </td>

                        <!-- ELO -->
                        <td class="py-4">
                            <span class="text-sm font-bold text-foreground">{{ player.elo }}</span>
                        </td>

                        <!-- V / D -->
                        <td class="py-4">
                            <span class="text-sm">
                                <span class="font-medium text-primary">{{ player.wins }}V</span>
                                <span class="text-muted-foreground"> / </span>
                                <span class="font-medium text-destructive">{{ player.losses }}D</span>
                            </span>
                        </td>

                        <!-- Tendance -->
                        <td class="py-4">
                            <div class="flex items-center gap-1 text-sm">
                                <template v-if="player.trend > 0">
                                    <TrendingUp class="h-4 w-4 text-primary" />
                                    <span class="font-medium text-primary">+{{ player.trend }}</span>
                                </template>
                                <template v-else-if="player.trend < 0">
                                    <TrendingDown class="h-4 w-4 text-destructive" />
                                    <span class="font-medium text-destructive">{{ player.trend }}</span>
                                </template>
                                <template v-else>
                                    <Minus class="h-4 w-4 text-muted-foreground" />
                                    <span class="text-muted-foreground">0</span>
                                </template>
                            </div>
                        </td>

                        <!-- Win Rate -->
                        <td class="py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-2 flex-1 rounded-full bg-gray-100">
                                    <div
                                        class="h-full rounded-full transition-all"
                                        :class="getWinRateColor(player.winRate)"
                                        :style="{ width: player.winRate + '%' }"
                                    />
                                </div>
                                <span class="w-10 text-right text-sm font-medium text-muted-foreground">{{ player.winRate }}%</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Empty state -->
            <div v-if="players.length === 0" class="py-12 text-center">
                <p class="text-sm text-muted-foreground">Aucun joueur dans le classement pour le moment.</p>
            </div>
        </div>
    </div>
</template>
