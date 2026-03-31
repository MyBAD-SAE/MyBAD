<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AdminClassPicker from '@/Components/admin/AdminClassPicker.vue';
import { Switch } from '@/Components/ui/switch';
import { Sparkles, ArrowLeft, RotateCcw, Save, Globe, Lightbulb, Trophy } from 'lucide-vue-next';

const props = defineProps({
    classes: { type: Array, default: () => [] },
    selectedClassId: { type: Number, default: null },
    parameters: { type: Array, default: () => [] },
});

const defaultPoints = [-0.7, -0.2, 0.0, 0.5, 1.0, 1.6];

const enableRankingGroups = ref(false);
const enableEloHandicap = ref(false);

const form = useForm({
    parameters: props.parameters.map(p => ({ id: p.id, winner_points: p.winnerPoints })),
});

const getDotColor = (value) => {
    if (value < 0) return 'bg-red-400';
    if (value > 0) return 'bg-emerald-400';
    return 'bg-gray-400';
};

const getPointColor = (value) => {
    if (value < 0) return 'text-red-500';
    if (value > 0) return 'text-emerald-500';
    return 'text-muted-foreground';
};

const getPointBg = (value) => {
    if (value < 0) return 'bg-red-50';
    if (value > 0) return 'bg-emerald-50';
    return 'bg-gray-100';
};

const getDescription = (param) => {
    if (param.minDiff < 0 && param.maxDiff < 0) return 'Vainqueur moins bien classé';
    if (param.minDiff <= 0 && param.maxDiff >= 0) return 'Classements proches';
    return 'Vainqueur mieux classé';
};

const resetDefaults = () => {
    form.parameters.forEach((p, i) => {
        p.winner_points = defaultPoints[i] ?? p.winner_points;
    });
    enableRankingGroups.value = false;
    enableEloHandicap.value = false;
};

const save = () => {
    form.put(route('admin.regles.update'));
};
</script>

<template>
    <Head title="Règles et défis - Admin" />

    <AdminLayout>
        <div class="p-8">
            <!-- Back link -->
            <Link :href="route('admin.dashboard')" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground mb-5">
                <ArrowLeft class="h-4 w-4" />
                Retour au dashboard
            </Link>

            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-100">
                        <Trophy class="h-6 w-6 text-red-500" />
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-foreground">Règles et défis</h1>
                        <p class="text-sm text-muted-foreground">Configurer les points ELO et les règles de défis</p>
                    </div>
                </div>
                <AdminClassPicker :classes="classes" :selected-class-id="selectedClassId" />
            </div>

            <!-- Points ELO section -->
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                    <Sparkles class="h-4 w-4 text-amber-500" />
                    <h2 class="text-sm font-semibold text-foreground">Points ELO</h2>
                </div>

                <div class="rounded-2xl border border-border overflow-hidden">
                    <!-- Table header -->
                    <div class="flex items-center px-5 py-3 border-b border-border">
                        <span class="flex-1 text-xs font-medium uppercase tracking-wider text-muted-foreground">Écart de classement</span>
                        <span class="w-32 text-right text-xs font-medium uppercase tracking-wider text-muted-foreground">Points du vainqueur</span>
                    </div>

                    <!-- Parameter rows -->
                    <div
                        v-for="(param, index) in parameters"
                        :key="param.id"
                        class="flex items-center px-5 py-4"
                        :class="index < parameters.length - 1 ? 'border-b border-border' : ''"
                    >
                        <div class="flex items-center gap-3 flex-1">
                            <span class="h-2.5 w-2.5 rounded-full shrink-0" :class="getDotColor(param.winnerPoints)" />
                            <div>
                                <p class="text-sm font-semibold text-foreground">{{ param.minDiff }} à {{ param.maxDiff }}</p>
                                <p class="text-xs text-muted-foreground">{{ getDescription(param) }}</p>
                            </div>
                        </div>
                        <div class="w-32 flex justify-end">
                            <input
                                v-model.number="form.parameters[index].winner_points"
                                type="number"
                                step="0.1"
                                class="w-16 rounded-lg px-3 py-1.5 text-sm font-semibold text-center border-0 outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                :class="[getPointColor(form.parameters[index].winner_points), getPointBg(form.parameters[index].winner_points)]"
                            />
                        </div>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="flex items-center justify-end gap-3 mt-4">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-full border border-border bg-white px-5 py-2.5 text-sm font-medium text-foreground hover:bg-gray-50 transition-colors"
                        @click="resetDefaults"
                    >
                        <RotateCcw class="h-4 w-4" />
                        Réinitialiser
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-full bg-primary px-6 py-2.5 text-sm font-medium text-white hover:bg-primary/90 transition-colors"
                        :disabled="form.processing"
                        @click="save"
                    >
                        <Save class="h-4 w-4" />
                        Sauvegarder les défis
                    </button>
                </div>
            </div>

            <!-- Défis section -->
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-muted-foreground mb-4">Défis</p>

                <div class="space-y-3">
                    <!-- Groupes de classement -->
                    <div class="flex items-center gap-4 rounded-2xl border border-border px-5 py-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100">
                            <Globe class="h-5 w-5 text-emerald-600" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-foreground">Groupes de classement</p>
                            <p class="text-xs text-muted-foreground">Les joueurs ne peuvent défier que ceux de leur groupe. Les groupes sont formés par tranche de classement.</p>
                        </div>
                        <Switch
                            :checked="enableRankingGroups"
                            class="h-7 w-12 shrink-0"
                            @update:checked="enableRankingGroups = $event"
                        />
                    </div>

                    <!-- Handicap d'ELO -->
                    <div class="flex items-center gap-4 rounded-2xl border border-border px-5 py-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100">
                            <Lightbulb class="h-5 w-5 text-amber-600" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-foreground">Handicap d'ELO</p>
                            <p class="text-xs text-muted-foreground">Si un joueur défie quelqu'un classé plus bas, il démarre le match avec un retard de points proportionnel à l'écart de rang.</p>
                        </div>
                        <Switch
                            :checked="enableEloHandicap"
                            class="h-7 w-12 shrink-0"
                            @update:checked="enableEloHandicap = $event"
                        />
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="flex items-center justify-end gap-3 mt-4">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-full border border-border bg-white px-5 py-2.5 text-sm font-medium text-foreground hover:bg-gray-50 transition-colors"
                        @click="resetDefaults"
                    >
                        <RotateCcw class="h-4 w-4" />
                        Réinitialiser
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-full bg-primary px-6 py-2.5 text-sm font-medium text-white hover:bg-primary/90 transition-colors"
                        :disabled="form.processing"
                        @click="save"
                    >
                        <Save class="h-4 w-4" />
                        Sauvegarder les défis
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
