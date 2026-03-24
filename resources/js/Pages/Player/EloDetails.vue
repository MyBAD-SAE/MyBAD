<script setup>
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PlayerLayout from '@/Layouts/PlayerLayout.vue';
import BottomNavBar from '@/Components/BottomNavBar.vue';
import { Card, CardContent } from '@/Components/ui/card';
import { Badge } from '@/Components/ui/badge';
import { ArrowLeft, TrendingUp, Trophy } from 'lucide-vue-next';

const props = defineProps({
    currentElo: { type: Number, default: 0 },
    bestElo: { type: Number, default: 0 },
    eloWeekDiff: { type: Number, default: 0 },
    eloHistory: { type: Array, default: () => [] },
    rank: { type: Number, default: null },
    totalPlayers: { type: Number, default: 0 },
});

const period = ref('30j');

const filteredHistory = computed(() => {
    if (period.value === 'tout' || props.eloHistory.length === 0) return props.eloHistory;
    // Keep last 30 entries for "30j"
    return props.eloHistory.slice(-30);
});

// Chart dimensions
const svgWidth = 300;
const svgHeight = 140;

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
    const data = filteredHistory.value;
    if (data.length < 2) return { line: '', area: '', points: [], yLabels: [], xLabels: [] };

    const values = data.map(d => d.elo);
    const min = Math.min(...values);
    const max = Math.max(...values);
    const range = max - min || 1;
    const padding = svgHeight * 0.12;

    const points = values.map((v, i) => ({
        x: (i / (values.length - 1)) * svgWidth,
        y: padding + (1 - (v - min) / range) * (svgHeight - padding * 2),
        value: v,
    }));

    const line = catmullRomToBezier(points);
    const area = `${line} L${svgWidth},${svgHeight} L0,${svgHeight} Z`;

    // Y-axis labels (4 labels)
    const step = range / 3;
    const yLabels = Array.from({ length: 4 }, (_, i) => {
        const val = Math.round(min + step * (3 - i));
        const y = padding + (1 - (val - min) / range) * (svgHeight - padding * 2);
        return { value: val, y };
    });

    // X-axis labels (first and last date)
    const xLabels = [];
    if (data.length > 0) {
        xLabels.push({ label: data[0].date, x: 0 });
        xLabels.push({ label: data[data.length - 1].date, x: svgWidth });
    }

    return { line, area, points, yLabels, xLabels };
});
</script>

<template>
    <Head title="Classement ELO" />

    <PlayerLayout>
        <div class="pb-20">
            <div class="space-y-5 p-5">
                <!-- Header -->
                <div class="flex items-center gap-3">
                    <Link :href="route('home')" class="flex h-10 w-10 items-center justify-center rounded-full border border-border">
                        <ArrowLeft class="h-5 w-5 text-foreground" />
                    </Link>
                    <h1 class="text-xl font-bold text-foreground">Classement ELO</h1>
                </div>

                <!-- Current ELO Card -->
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#27BDAE] to-[#5DC9BC] p-6 text-white">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-white/90">Mon ELO actuel</span>
                        <Badge
                            v-if="eloWeekDiff !== 0"
                            class="border-0 bg-white/20 text-white hover:bg-white/30"
                        >
                            <TrendingUp v-if="eloWeekDiff > 0" class="mr-1 h-3 w-3" />
                            {{ eloWeekDiff > 0 ? '+' : '' }}{{ eloWeekDiff }} cette semaine
                        </Badge>
                    </div>
                    <p class="mt-3 text-5xl font-bold">{{ currentElo }}</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 gap-3">
                    <!-- Best ELO -->
                    <Card class="shadow-none">
                        <CardContent class="p-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10">
                                <TrendingUp class="h-5 w-5 text-primary" />
                            </div>
                            <p class="mt-3 text-2xl font-bold text-foreground">{{ bestElo }}</p>
                            <p class="text-sm text-muted-foreground">Meilleur ELO</p>
                        </CardContent>
                    </Card>

                    <!-- Rank -->
                    <Card class="shadow-none">
                        <CardContent class="p-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100">
                                <Trophy class="h-5 w-5 text-orange-500" />
                            </div>
                            <p class="mt-3 text-2xl font-bold text-foreground">#{{ rank ?? '-' }}</p>
                            <p class="text-sm text-muted-foreground">Classement</p>
                            <p v-if="totalPlayers > 0" class="text-xs font-medium text-primary">sur {{ totalPlayers }} joueurs</p>
                        </CardContent>
                    </Card>
                </div>

                <!-- Evolution Chart -->
                <Card class="shadow-none">
                    <CardContent class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-foreground">Evolution ELO</h3>
                                <p class="text-sm text-muted-foreground">Progression sur la période</p>
                            </div>
                            <div class="flex items-center gap-1 rounded-full border border-border p-1">
                                <button
                                    class="rounded-full px-3 py-1 text-xs font-medium transition-colors"
                                    :class="period === '30j' ? 'bg-foreground text-background' : 'text-muted-foreground'"
                                    @click="period = '30j'"
                                >
                                    30j
                                </button>
                                <button
                                    class="rounded-full px-3 py-1 text-xs font-medium transition-colors"
                                    :class="period === 'tout' ? 'bg-foreground text-background' : 'text-muted-foreground'"
                                    @click="period = 'tout'"
                                >
                                    Tout
                                </button>
                            </div>
                        </div>

                        <!-- Chart -->
                        <div v-if="filteredHistory.length >= 2" class="relative mt-4 h-[180px] w-full">
                            <!-- Y-axis labels -->
                            <div class="absolute left-0 top-0 bottom-6 flex flex-col justify-between">
                                <span
                                    v-for="label in chartData.yLabels"
                                    :key="label.value"
                                    class="text-[10px] text-muted-foreground"
                                >
                                    {{ label.value }}
                                </span>
                            </div>

                            <!-- SVG Chart -->
                            <div class="ml-10 h-[150px] w-[calc(100%-40px)]">
                                <svg
                                    class="h-full w-full"
                                    :viewBox="`0 0 ${svgWidth} ${svgHeight}`"
                                    preserveAspectRatio="none"
                                >
                                    <defs>
                                        <linearGradient id="eloDetailGradient" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stop-color="#27BDAE" stop-opacity="0.3" />
                                            <stop offset="100%" stop-color="#27BDAE" stop-opacity="0.02" />
                                        </linearGradient>
                                    </defs>

                                    <!-- Grid lines -->
                                    <line
                                        v-for="label in chartData.yLabels"
                                        :key="'grid-' + label.value"
                                        :x1="0"
                                        :y1="label.y"
                                        :x2="svgWidth"
                                        :y2="label.y"
                                        stroke="currentColor"
                                        class="text-border"
                                        stroke-width="0.5"
                                        stroke-dasharray="4 4"
                                    />

                                    <path :d="chartData.area" fill="url(#eloDetailGradient)" />
                                    <path :d="chartData.line" fill="none" stroke="#27BDAE" stroke-width="2.5" />
                                </svg>
                            </div>

                            <!-- X-axis labels -->
                            <div class="ml-10 flex justify-between">
                                <span
                                    v-for="label in chartData.xLabels"
                                    :key="label.label"
                                    class="text-[10px] text-muted-foreground"
                                >
                                    {{ label.label }}
                                </span>
                            </div>
                        </div>

                        <!-- No data -->
                        <div v-else class="mt-4 py-8 text-center">
                            <p class="text-sm text-muted-foreground">Pas assez de données pour afficher le graphique.</p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <BottomNavBar active="dashboard" />
    </PlayerLayout>
</template>
