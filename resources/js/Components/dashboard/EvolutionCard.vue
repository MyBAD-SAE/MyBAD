<script setup>
import { Card, CardContent } from '@/Components/ui/card';
import { computed } from 'vue';

const props = defineProps({
    wins: { type: Number, default: 0 },
    losses: { type: Number, default: 0 },
    total: { type: Number, default: 0 },
});

const winRate = computed(() => {
    if (props.total === 0) return 0;
    return Math.round((props.wins / props.total) * 100);
});

// SVG gauge arc calculations
const radius = 40;
const circumference = Math.PI * radius; // semi-circle
const dashOffset = computed(() => {
    return circumference - (winRate.value / 100) * circumference;
});
</script>

<template>
    <Card class="shadow-none">
        <CardContent class="p-4">
            <span class="text-sm font-medium text-muted-foreground">Evolution</span>
            <!-- Gauge -->
            <div class="mt-2 flex justify-center">
                <svg width="120" height="70" viewBox="0 0 120 70">
                    <!-- Background arc -->
                    <path
                        d="M 10 65 A 40 40 0 0 1 110 65"
                        fill="none"
                        stroke="hsl(var(--muted))"
                        stroke-width="8"
                        stroke-linecap="round"
                    />
                    <!-- Progress arc -->
                    <path
                        d="M 10 65 A 40 40 0 0 1 110 65"
                        fill="none"
                        stroke="hsl(var(--primary))"
                        stroke-width="8"
                        stroke-linecap="round"
                        :stroke-dasharray="circumference"
                        :stroke-dashoffset="dashOffset"
                    />
                    <!-- Center text -->
                    <text x="60" y="52" text-anchor="middle" class="text-lg font-bold fill-foreground" font-size="18">
                        {{ winRate }}%
                    </text>
                    <text x="60" y="66" text-anchor="middle" class="fill-muted-foreground" font-size="10">
                        Victoires
                    </text>
                </svg>
            </div>
            <!-- Stats row -->
            <div class="mt-2 flex justify-center gap-4 text-center text-xs">
                <div>
                    <span class="block text-base font-bold text-primary">{{ wins }}</span>
                    <span class="text-muted-foreground">Gagnés</span>
                </div>
                <div>
                    <span class="block text-base font-bold text-destructive">{{ losses }}</span>
                    <span class="text-muted-foreground">Perdus</span>
                </div>
                <div>
                    <span class="block text-base font-bold text-foreground">{{ total }}</span>
                    <span class="text-muted-foreground">Total</span>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
