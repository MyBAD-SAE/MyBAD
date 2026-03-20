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

const cx = 70;
const cy = 65;
const r = 45;
const strokeWidth = 10;

function angleToPoint(angleDeg) {
    const rad = (angleDeg * Math.PI) / 180;
    return {
        x: cx + r * Math.cos(rad),
        y: cy - r * Math.sin(rad),
    };
}

const bgArc = computed(() => {
    const start = angleToPoint(180);
    const end = angleToPoint(0);
    return `M ${start.x} ${start.y} A ${r} ${r} 0 1 1 ${end.x} ${end.y}`;
});

const progressArc = computed(() => {
    if (winRate.value === 0) return '';
    const start = angleToPoint(180);
    const endAngle = 180 - (winRate.value / 100) * 180;
    const end = angleToPoint(endAngle);
    return `M ${start.x} ${start.y} A ${r} ${r} 0 0 1 ${end.x} ${end.y}`;
});

const dotPosition = computed(() => {
    const endAngle = 180 - (winRate.value / 100) * 180;
    return angleToPoint(endAngle);
});
</script>

<template>
    <Card class="shadow-none">
        <CardContent class="p-4">
            <span class="text-sm font-bold text-foreground">Evolution</span>

            <div class="mt-2 flex justify-center">
                <svg width="140" height="85" viewBox="0 0 140 85">
                    <path
                        :d="bgArc"
                        fill="none"
                        stroke="var(--muted)"
                        :stroke-width="strokeWidth"
                        stroke-linecap="round"
                    />
                    <path
                        v-if="winRate > 0"
                        :d="progressArc"
                        fill="none"
                        stroke="var(--primary)"
                        :stroke-width="strokeWidth"
                        stroke-linecap="round"
                    />
                    <circle
                        v-if="winRate > 0"
                        :cx="dotPosition.x"
                        :cy="dotPosition.y"
                        r="7"
                        fill="white"
                        stroke="var(--primary)"
                        stroke-width="3"
                    />
                    <text
                        :x="cx"
                        y="55"
                        text-anchor="middle"
                        font-size="20"
                        font-weight="bold"
                        fill="var(--foreground)"
                    >
                        {{ winRate }}%
                    </text>
                    <text
                        :x="cx"
                        y="70"
                        text-anchor="middle"
                        font-size="10"
                        fill="var(--muted-foreground)"
                    >
                        Victoires
                    </text>
                </svg>
            </div>

            <div class="flex justify-center gap-4 text-center text-xs">
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
