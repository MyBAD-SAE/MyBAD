<script setup>
import { Card, CardContent } from '@/Components/ui/card';
import { TrendingUp } from 'lucide-vue-next';

defineProps({
    elo: { type: Number, default: 100.0 },
    eloDiff: { type: Number, default: 0 },
});
</script>

<template>
    <Card>
        <CardContent class="p-4">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-muted-foreground">ELO</span>
                <div v-if="eloDiff !== 0" class="flex items-center gap-1 text-sm font-medium" :class="eloDiff > 0 ? 'text-primary' : 'text-destructive'">
                    <TrendingUp class="h-3.5 w-3.5" />
                    <span>{{ eloDiff > 0 ? '+' : '' }}{{ eloDiff }}</span>
                </div>
            </div>
            <p class="mt-1 text-4xl font-bold text-foreground">{{ elo.toFixed(1) }}</p>
            <!-- Mini chart placeholder -->
            <div class="mt-4 h-16 w-full overflow-hidden rounded-lg bg-primary/5">
                <svg class="h-full w-full" viewBox="0 0 200 60" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="eloGradient" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="hsl(var(--primary))" stop-opacity="0.3" />
                            <stop offset="100%" stop-color="hsl(var(--primary))" stop-opacity="0.05" />
                        </linearGradient>
                    </defs>
                    <path d="M0,45 Q30,40 60,35 T120,25 T200,20 L200,60 L0,60 Z" fill="url(#eloGradient)" />
                    <path d="M0,45 Q30,40 60,35 T120,25 T200,20" fill="none" stroke="hsl(var(--primary))" stroke-width="2" />
                </svg>
            </div>
        </CardContent>
    </Card>
</template>
