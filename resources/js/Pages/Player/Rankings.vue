<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import PlayerLayout from '@/Layouts/PlayerLayout.vue';
import BottomNavBar from '@/Components/player/BottomNavBar.vue';
import ClassPicker from '@/Components/player/dashboard/ClassPicker.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Trophy, TrendingUp, TrendingDown, ArrowLeft, Medal } from 'lucide-vue-next';

const currentUserId = usePage().props.auth.user?.id;

defineProps({
    players: { type: Array, default: () => [] },
    classes: { type: Array, default: () => [] },
    selectedClassId: { type: Number, default: null },
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

const getMedalColor = (rank) => {
    if (rank === 1) return '#EAB308'; // or
    if (rank === 2) return '#9CA3AF'; // argent
    if (rank === 3) return '#CD7F32'; // bronze
    return null;
};

const getRankBg = (rank) => {
    if (rank === 1) return 'border-color: #EAB308;'; // or
    if (rank === 2) return 'border-color: #9CA3AF;'; // argent
    if (rank === 3) return 'border-color: #CD7F32;'; // bronze
    return '';
};
</script>

<template>
    <Head title="Classement" />

    <PlayerLayout>
        <div class="pb-20">
            <!-- Header -->
            <div class="px-4 pt-6 pb-4">
                <div class="relative flex items-center justify-center">
                    <Link :href="route('home')" class="absolute left-0 w-9 h-9 rounded-xl flex items-center justify-center hover:bg-gray-50 transition-colors" style="background-color: #ffffff; border: 1px solid #e5e7eb;">
                        <ArrowLeft class="h-4 w-4 text-foreground" />
                    </Link>
                    <h1 v-if="classes.length < 2" class="text-lg font-bold">Classement</h1>
                    <ClassPicker v-if="classes.length >= 2" class="absolute right-0" :classes="classes" :selected-class-id="selectedClassId" />
                </div>
                <h1 v-if="classes.length >= 2" class="text-lg font-bold text-center mt-3">Classement</h1>
            </div>

            <div class="px-5 pb-5">
                <div>
                        <!-- Ranking list -->
                        <div class="mt-4 space-y-2">
                            <div
                                v-for="player in players"
                                :key="player.rank"
                                class="flex items-center gap-3 rounded-xl border p-3"
                                :class="{ 'bg-primary/5 ring-2 ring-primary/30': player.userId === currentUserId }"
                                :style="getRankBg(player.rank)"
                            >
                                <!-- Rank -->
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center">
                                    <Medal v-if="player.rank <= 3" class="h-5 w-5" :style="{ color: getMedalColor(player.rank) }" />
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
                                        <template v-if="player.trend !== 0">
                                            <component :is="player.trend > 0 ? TrendingUp : TrendingDown" class="h-3 w-3" :class="player.trend > 0 ? 'text-primary' : 'text-destructive'" />
                                            <span :class="player.trend > 0 ? 'text-primary' : 'text-destructive'">{{ player.trend > 0 ? '+' : '' }}{{ player.trend }}</span>
                                        </template>
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
                </div>
            </div>
        </div>

        <BottomNavBar active="classement" />
    </PlayerLayout>
</template>
