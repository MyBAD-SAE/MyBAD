<script setup>
import { Link } from '@inertiajs/vue3';
import { Card, CardContent } from '@/Components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Trophy, TrendingUp, TrendingDown, ChevronRight } from 'lucide-vue-next';

defineProps({
    players: {
        type: Array,
        default: () => [],
    },
});

const getInitials = (name) => {
    const parts = name.split(' ');
    return parts.map(p => p.charAt(0)).join('');
};

const getTrendColor = (winRate) => {
    if (winRate >= 70) return 'bg-primary';
    if (winRate >= 50) return 'bg-orange-400';
    return 'bg-destructive';
};

const podiumOrder = [1, 0, 2]; // 2nd, 1st, 3rd
</script>

<template>
    <Card class="shadow-none">
        <CardContent class="p-4">
            <h3 class="text-lg font-bold text-foreground">Classement</h3>
            <p class="text-sm text-muted-foreground">{{ players.length }} joueurs · saison en cours</p>

            <!-- Podium -->
            <div v-if="players.length >= 3" class="mt-4 flex items-end justify-center gap-2">
                <div v-for="idx in podiumOrder" :key="idx" class="flex flex-col items-center" :class="idx === 0 ? 'order-2' : idx === 1 ? 'order-1' : 'order-3'">
                    <div class="relative">
                        <span v-if="idx === 0" class="absolute -top-5 left-1/2 z-10 -translate-x-1/2 text-xl">👑</span>
                        <Avatar :class="idx === 0 ? 'h-16 w-16 ring-2 ring-yellow-400' : 'h-12 w-12'">
                            <AvatarImage v-if="players[idx]?.avatar" :src="players[idx].avatar" />
                            <AvatarFallback class="text-xs">{{ getInitials(players[idx]?.name || '') }}</AvatarFallback>
                        </Avatar>
                    </div>
                    <span class="mt-1 text-xs font-medium text-foreground">{{ players[idx]?.name.split(' ')[0] }}</span>
                    <span class="text-xs text-muted-foreground">{{ players[idx]?.elo }} pts</span>
                    <div
                        class="mt-1 w-20 rounded-t-lg"
                        :class="idx === 0 ? 'h-16 bg-primary/15' : idx === 1 ? 'h-12 bg-muted' : 'h-10 bg-muted'"
                    >
                        <div class="flex h-full items-end justify-center pb-1">
                            <span class="text-xs font-semibold text-muted-foreground">{{ idx === 0 ? '1er' : `${idx + 1}e` }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ranking list -->
            <div class="mt-4 space-y-2">
                <div
                    v-for="player in players"
                    :key="player.rank"
                    class="flex items-center gap-3 rounded-xl border p-3"
                >
                    <!-- Rank -->
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center">
                        <Trophy v-if="player.rank === 1" class="h-5 w-5 text-yellow-500" />
                        <span v-else class="text-sm font-semibold text-muted-foreground">{{ player.rank }}</span>
                    </div>

                    <!-- Avatar -->
                    <Avatar class="h-9 w-9 shrink-0">
                        <AvatarImage v-if="player.avatar" :src="player.avatar" />
                        <AvatarFallback class="text-xs">{{ getInitials(player.name) }}</AvatarFallback>
                    </Avatar>

                    <!-- Info -->
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-foreground truncate">{{ player.name }}</p>
                        <div class="flex items-center gap-1 text-xs text-muted-foreground">
                            <span class="text-primary">{{ player.wins }}V · {{ player.losses }}D</span>
                            <component :is="player.trend >= 0 ? TrendingUp : TrendingDown" class="h-3 w-3" :class="player.trend >= 0 ? 'text-primary' : 'text-destructive'" />
                            <span :class="player.trend >= 0 ? 'text-primary' : 'text-destructive'">{{ player.trend > 0 ? '+' : '' }}{{ player.trend }}</span>
                        </div>
                    </div>

                    <!-- ELO & Win rate -->
                    <div class="shrink-0 text-right">
                        <p class="text-sm font-bold text-foreground">{{ player.elo }}</p>
                        <div class="flex items-center justify-end gap-1">
                            <div class="h-2 w-2 rounded-full" :class="getTrendColor(player.winRate)" />
                            <span class="text-xs text-muted-foreground">{{ player.winRate }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-if="players.length === 0" class="py-8 text-center">
                <p class="text-sm text-muted-foreground">Aucun joueur dans le classement pour le moment.</p>
            </div>

            <!-- Link -->
            <Link :href="route('classements')" class="mt-4 flex w-full items-center justify-center gap-1 text-sm font-medium text-primary">
                Voir tout le classement
                <ChevronRight class="h-4 w-4" />
            </Link>
        </CardContent>
    </Card>
</template>
