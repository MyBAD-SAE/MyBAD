<script setup>
import { computed, ref } from 'vue';
import { Card, CardContent } from '@/Components/ui/card';
import { TrendingUp, TrendingDown } from 'lucide-vue-next';

const props = defineProps({
    elo: { type: Number, default: 100.0 },
    eloDiff: { type: Number, default: 0 },
    history: { type: Array, default: () => [] },
    hasMatches: { type: Boolean, default: true },
});

const selectedIndex = ref(null);

const svgWidth = 300;
const svgHeight = 120;

function catmullRomToBezier(points) {
    const d = [];
    for (let i = 0; i < points.length - 1; i++) {
        const p0 = points[Math.max(i - 1, 0)];
        const p1 = points[i];
        const p2 = points[i + 1];
        const p3 = points[Math.min(i + 2, points.length - 1)];

        const cp1x = p1.x + (p2.x - p0.x) / 6;
        const cp1y = p1.y + (p2.y - p0.y) / 6;
        const cp2x = p2.x - (p3.x - p1.x) / 6;
        const cp2y = p2.y - (p3.y - p1.y) / 6;

        if (i === 0) d.push(`M${p1.x},${p1.y}`);
        d.push(`C${cp1x},${cp1y} ${cp2x},${cp2y} ${p2.x},${p2.y}`);
    }
    return d.join(' ');
}

const chartData = computed(() => {
    if (props.history.length < 2) return { line: '', area: '', points: [] };

    const values = props.history.map(Number);
    const min = Math.min(...values);
    const max = Math.max(...values);
    const range = max - min || 1;
    const padding = svgHeight * 0.15;

    const points = values.map((v, i) => ({
        x: (i / (values.length - 1)) * svgWidth,
        y: padding + (1 - (v - min) / range) * (svgHeight - padding * 2),
        value: v,
    }));

    const line = catmullRomToBezier(points);
    const area = `${line} L${svgWidth},${svgHeight} L0,${svgHeight} Z`;

    return { line, area, points };
});
</script>

<template>
    <Card class="shadow-none py-0 overflow-hidden">
        <CardContent class="p-4" :class="{ 'pb-0': hasMatches }">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm font-bold text-foreground">ELO</span>
                    <p class="text-xs text-muted-foreground">Classement</p>
                </div>
                <div
                    v-if="hasMatches && eloDiff !== 0"
                    class="flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold"
                    :class="eloDiff > 0 ? 'bg-primary/10 text-primary' : 'bg-destructive/10 text-destructive'"
                >
                    <TrendingUp v-if="eloDiff > 0" class="h-3 w-3" />
                    <TrendingDown v-else class="h-3 w-3" />
                    <span>{{ eloDiff > 0 ? '+' : '' }}{{ eloDiff }}</span>
                </div>
                <span v-if="!hasMatches" class="rounded-full bg-muted px-3 py-1 text-xs font-medium text-muted-foreground">Nouveau joueur</span>
            </div>
            <p class="mt-2 text-4xl font-bold text-foreground">{{ elo }}</p>
        </CardContent>

        <!-- Pas de matchs : ligne pointillée + message -->
        <div v-if="!hasMatches" class="px-4 pb-4">
            <div class="border-t border-dashed border-muted-foreground/30 pt-3">
                <p class="text-xs text-muted-foreground text-center">En attente du premier match</p>
            </div>
        </div>

        <div v-if="hasMatches" class="relative h-[120px] w-full">
            <svg
                v-if="history.length >= 2"
                class="absolute inset-0 h-full w-full"
                :viewBox="`0 0 ${svgWidth} ${svgHeight}`"
                preserveAspectRatio="none"
            >
                <defs>
                    <linearGradient id="eloAreaGradient" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="var(--primary)" stop-opacity="0.4" />
                        <stop offset="100%" stop-color="var(--primary)" stop-opacity="0.02" />
                    </linearGradient>
                </defs>
                <path :d="chartData.area" fill="url(#eloAreaGradient)" />
                <path :d="chartData.line" fill="none" stroke="var(--primary)" stroke-width="2.5" />
            </svg>
            <!-- Points cliquables -->
            <button
                v-for="(point, i) in chartData.points"
                v-show="i !== 0 && i !== chartData.points.length - 1"
                :key="i"
                class="absolute h-4 w-4 -translate-x-1/2 -translate-y-1/2 cursor-pointer rounded-full flex items-center justify-center"
                :style="{
                    left: (point.x / svgWidth * 100) + '%',
                    top: (point.y / svgHeight * 100) + '%',
                }"
                @click="selectedIndex = selectedIndex === i ? null : i"
            >
                <span
                    class="block h-2.5 w-2.5 rounded-full border-2 border-white bg-primary transition-transform"
                    :class="selectedIndex === i ? 'scale-125' : ''"
                />
                <!-- Tooltip -->
                <span
                    v-if="selectedIndex === i"
                    class="absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-md bg-foreground px-2 py-1 text-[10px] font-semibold text-background shadow-md"
                >
                    {{ point.value }}
                </span>
            </button>
        </div>
    </Card>
</template>
