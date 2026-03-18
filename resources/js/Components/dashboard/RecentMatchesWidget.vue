<script setup>
import { Card, CardContent } from '@/Components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Trophy, Clock, Frown, ChevronRight } from 'lucide-vue-next';

defineProps({
    matches: {
        type: Array,
        default: () => [
            { opponent: 'Quentin UGUEN', score: '15 - 7', eloDiff: 15, date: '10 mar.', won: true },
            { opponent: 'Amélie DUBOIS', score: '15 - 4', eloDiff: 22, date: '10 mar.', won: true },
            { opponent: 'Kenji TANAKA', score: '6 - 15', eloDiff: -12, date: '10 mar.', won: false },
            { opponent: 'Clara MARTIN', score: '15 - 12', eloDiff: 8, date: '10 mar.', won: true },
            { opponent: 'Hugo LEROUX', score: '4 - 15', eloDiff: -18, date: '10 mar.', won: false },
        ],
    },
});

const getInitials = (name) => {
    const parts = name.split(' ');
    return parts.map(p => p.charAt(0)).join('');
};
</script>

<template>
    <div>
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-foreground">Derniers matchs</h3>
                <p class="text-sm text-muted-foreground">{{ matches.length }} matchs récents</p>
            </div>
            <button class="flex items-center gap-1 text-sm font-medium text-primary">
                Tout voir
                <ChevronRight class="h-4 w-4" />
            </button>
        </div>

        <div class="mt-3 space-y-2">
            <div
                v-for="(match, index) in matches"
                :key="index"
                class="flex items-center gap-3 rounded-xl border p-3"
            >
                <!-- Win/Loss icon -->
                <div
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full"
                    :class="match.won ? 'bg-primary/10' : 'bg-destructive/10'"
                >
                    <Trophy v-if="match.won" class="h-4 w-4 text-primary" />
                    <Frown v-else class="h-4 w-4 text-destructive" />
                </div>

                <!-- Avatar -->
                <Avatar class="h-9 w-9 shrink-0">
                    <AvatarFallback class="text-xs">{{ getInitials(match.opponent) }}</AvatarFallback>
                </Avatar>

                <!-- Info -->
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-foreground truncate">{{ match.opponent }}</p>
                    <p class="text-xs text-muted-foreground">{{ match.score }}</p>
                </div>

                <!-- ELO diff & date -->
                <div class="shrink-0 text-right">
                    <p class="text-sm font-bold" :class="match.eloDiff > 0 ? 'text-primary' : 'text-destructive'">
                        {{ match.eloDiff > 0 ? '+' : '' }}{{ match.eloDiff }}
                    </p>
                    <p class="text-xs text-muted-foreground">{{ match.date }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
