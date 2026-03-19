<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PlayerLayout from '@/Layouts/PlayerLayout.vue';
import BottomNavBar from '@/Components/BottomNavBar.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { ArrowLeft, TrendingUp, TrendingDown } from 'lucide-vue-next';

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

const getWinRateBarColor = (winRate) => {
    if (winRate >= 70) return 'bg-primary';
    if (winRate >= 50) return 'bg-orange-400';
    return 'bg-destructive';
};

const podiumOrder = [1, 0, 2]; // 2nd, 1st, 3rd
</script>

<template>
    <Head title="Classement" />

    <PlayerLayout>
        <div class="pb-20">
            <!-- Header -->
            <div class="relative flex items-center justify-center px-5 pt-5 pb-2">
                <Link :href="route('home')" class="absolute left-5">
                    <ArrowLeft class="h-5 w-5 text-foreground" />
                </Link>
                <h1 class="text-lg font-bold text-foreground">Classement</h1>
            </div>

            <div class="px-5 pt-4">
                <!-- Title -->
                <h2 class="text-2xl font-bold text-foreground">Classement</h2>
                <p class="text-sm text-muted-foreground">{{ players.length }} joueurs</p>

                <!-- Podium -->
                <div class="mt-6 flex items-end justify-center gap-4">
                    <div
                        v-for="idx in podiumOrder"
                        :key="idx"
                        class="flex flex-col items-center"
                        :class="idx === 0 ? 'order-2' : idx === 1 ? 'order-1' : 'order-3'"
                    >
                        <div class="relative">
                            <Avatar :class="idx === 0 ? 'h-18 w-18 ring-4 ring-yellow-400' : 'h-14 w-14 ring-2 ring-muted'">
                                <AvatarImage v-if="players[idx]?.avatar" :src="players[idx].avatar" />
                                <AvatarFallback :class="idx === 0 ? 'text-sm' : 'text-xs'">{{ getInitials(players[idx]?.name || '') }}</AvatarFallback>
                            </Avatar>
                        </div>
                        <span class="mt-2 text-sm font-semibold text-foreground">{{ players[idx]?.name.split(' ')[0] }}</span>
                        <span class="text-xs text-muted-foreground">{{ players[idx]?.elo }} pts</span>
                        <div
                            class="mt-2 w-24 rounded-t-xl"
                            :class="idx === 0 ? 'h-20 bg-primary/20' : idx === 1 ? 'h-14 bg-primary/10' : 'h-12 bg-primary/10'"
                        >
                            <div class="flex h-full items-end justify-center pb-2">
                                <span class="text-sm font-bold text-muted-foreground">{{ idx === 0 ? '1er' : `${idx + 1}e` }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ranking list -->
                <div class="mt-6 space-y-3">
                    <div
                        v-for="player in players"
                        :key="player.rank"
                        class="flex items-center gap-3 rounded-2xl border border-border bg-card p-3"
                    >
                        <!-- Rank -->
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full"
                            :class="player.rank <= 3 ? 'bg-primary/10' : ''"
                        >
                            <span
                                class="text-sm font-bold"
                                :class="player.rank <= 3 ? 'text-primary' : 'text-muted-foreground'"
                            >{{ player.rank }}</span>
                        </div>

                        <!-- Avatar -->
                        <Avatar class="h-10 w-10 shrink-0">
                            <AvatarImage v-if="player.avatar" :src="player.avatar" />
                            <AvatarFallback class="text-xs">{{ getInitials(player.name) }}</AvatarFallback>
                        </Avatar>

                        <!-- Info -->
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-foreground truncate">{{ player.name }}</p>
                            <div class="mt-0.5 flex items-center gap-1.5">
                                <span class="rounded-full bg-primary/10 px-2 py-0.5 text-[10px] font-semibold text-primary">{{ player.wins }}V · {{ player.losses }}D</span>
                                <template v-if="player.trend !== 0">
                                    <component
                                        :is="player.trend > 0 ? TrendingUp : TrendingDown"
                                        class="h-3 w-3"
                                        :class="player.trend > 0 ? 'text-primary' : 'text-destructive'"
                                    />
                                    <span class="text-[10px] font-semibold" :class="player.trend > 0 ? 'text-primary' : 'text-destructive'">
                                        {{ player.trend > 0 ? '+' : '' }}{{ player.trend }}
                                    </span>
                                </template>
                            </div>
                        </div>

                        <!-- Elo & Win rate -->
                        <div class="shrink-0 text-right">
                            <p class="text-sm font-bold text-foreground">{{ player.elo }}</p>
                            <div class="mt-1 flex items-center justify-end gap-1.5">
                                <div class="h-1 w-10 overflow-hidden rounded-full bg-muted">
                                    <div class="h-full rounded-full" :class="getWinRateBarColor(player.winRate)" :style="{ width: player.winRate + '%' }" />
                                </div>
                                <span class="text-[10px] font-medium text-muted-foreground">{{ player.winRate }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <BottomNavBar active="classement" />
    </PlayerLayout>
</template>
