<script setup>
import { Link } from '@inertiajs/vue3';
import { Card, CardContent } from '@/Components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Trophy, Clock, Frown, ChevronRight } from 'lucide-vue-next';

defineProps({
    matches: {
        type: Array,
        default: () => [],
    },
});

const getInitials = (name) => {
    const parts = name.split(' ');
    return parts.map(p => p.charAt(0)).join('');
};
</script>

<template>
    <Card class="shadow-none">
        <CardContent class="p-4">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="text-lg font-semibold text-foreground">Derniers matchs</h3>
                    <p class="text-sm text-muted-foreground">{{ matches.length }} derniers matchs</p>
                </div>
                <Link :href="route('matches.history')" class="flex items-center gap-1 text-sm font-medium text-primary">
                    Tout voir
                    <ChevronRight class="h-4 w-4" />
                </Link>
            </div>

            <div class="space-y-2">
                <div
                    v-for="(match, index) in matches"
                    :key="index"
                    class="flex items-center gap-3 rounded-xl bg-[#F9FAFB] p-3"
                >
                <!-- Win/Loss icon -->
                <div
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full"
                    :class="match.result === 'win' ? 'bg-primary/10' : 'bg-destructive/10'"
                >
                    <Trophy v-if="match.result === 'win'" class="h-4 w-4 text-primary" />
                    <Frown v-else class="h-4 w-4 text-destructive" />
                </div>

                <!-- Avatar -->
                <Avatar class="h-9 w-9 shrink-0">
                    <AvatarFallback class="text-xs">{{ getInitials(match.opponent?.name ?? '') }}</AvatarFallback>
                </Avatar>

                <!-- Info -->
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-foreground truncate">{{ match.opponent?.name }}</p>
                    <p class="text-xs text-muted-foreground">{{ match.myScore }} – {{ match.opponentScore }}</p>
                </div>

                <!-- ELO diff & date -->
                <div class="shrink-0 text-right">
                    <p class="text-sm font-bold" :class="match.eloChange > 0 ? 'text-primary' : 'text-destructive'">
                        {{ match.eloChange > 0 ? '+' : '' }}{{ match.eloChange }}
                    </p>
                    <p class="text-xs text-muted-foreground">{{ match.date }}</p>
                </div>
            </div>
            </div>
        </CardContent>
    </Card>
</template>
