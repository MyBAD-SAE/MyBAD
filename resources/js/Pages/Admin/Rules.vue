<script setup>
import { ref, computed, watch } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { toast } from "vue-sonner";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminClassPicker from "@/Components/admin/AdminClassPicker.vue";
import { Switch } from "@/Components/ui/switch";
import {
    Zap,
    ArrowLeft,
    RotateCcw,
    Save,
    Lightbulb,
    Trophy,
    Minus,
    Plus,
    Info,
    Layers,
} from "lucide-vue-next";

const props = defineProps({
    classes: { type: Array, default: () => [] },
    selectedClassId: { type: Number, default: null },
    parameters: { type: Array, default: () => [] },
    rule: { type: Object, default: null },
});

const defaultPoints = [-0.7, -0.2, 0.0, 0.5, 1.0, 1.6];

const defaultHandicapRanges = [
    { from: 1, to: 3, points: 0 },
    { from: 3, to: 6, points: -3 },
    { from: 6, to: 9, points: -5 },
    { from: 10, to: null, points: -8 },
];

const enableRankingGroups = ref(props.rule?.enableRankingGroups ?? false);
const enableEloHandicap = ref(props.rule?.enableEloHandicap ?? false);
const groupSize = ref(props.rule?.groupSize ?? 8);
const totalPlayers = 24;

const handicapRanges = ref(
    props.rule?.handicapParameters?.length
        ? props.rule.handicapParameters.map((p) => ({
              from: p.from,
              to: p.to,
              points: p.points,
          }))
        : defaultHandicapRanges.map((r) => ({ ...r }))
);

const groupColors = [
    { border: 'border-emerald-200', bg: 'bg-emerald-50/60', text: 'text-emerald-500' },
    { border: 'border-amber-200', bg: 'bg-amber-50/60', text: 'text-amber-500' },
    { border: 'border-purple-200', bg: 'bg-purple-50/60', text: 'text-purple-500' },
    { border: 'border-blue-200', bg: 'bg-blue-50/60', text: 'text-blue-500' },
    { border: 'border-rose-200', bg: 'bg-rose-50/60', text: 'text-rose-500' },
    { border: 'border-cyan-200', bg: 'bg-cyan-50/60', text: 'text-cyan-500' },
];

const groups = computed(() => {
    const count = Math.ceil(totalPlayers / groupSize.value);
    return Array.from({ length: count }, (_, i) => ({
        name: `Groupe ${i + 1}`,
        from: i * groupSize.value + 1,
        to: Math.min((i + 1) * groupSize.value, totalPlayers),
        color: groupColors[i % groupColors.length],
    }));
});

const form = useForm({
    parameters: props.parameters.map((p) => ({
        id: p.id,
        winner_points: p.winnerPoints,
    })),
});

const ruleForm = useForm({
    enable_ranking_groups: false,
    enable_elo_handicap: false,
    group_size: 8,
    handicap_parameters: [],
});

const getDotColor = (value) => {
    if (value < 0) return "bg-red-400";
    if (value > 0) return "bg-emerald-400";
    return "bg-gray-400";
};

const getPointColor = (value) => {
    if (value < 0) return "text-red-500";
    if (value > 0) return "text-emerald-500";
    return "text-muted-foreground";
};

const getPointBg = (value) => {
    if (value < 0) return "bg-red-50";
    if (value > 0) return "bg-emerald-50";
    return "bg-gray-100";
};

const getDescription = (param) => {
    if (param.minDiff < 0 && param.maxDiff < 0)
        return "Vainqueur moins bien classé";
    if (param.minDiff <= 0 && param.maxDiff >= 0) return "Classements proches";
    return "Vainqueur mieux classé";
};

const resetDefaults = () => {
    form.parameters.forEach((p, i) => {
        p.winner_points = defaultPoints[i] ?? p.winner_points;
    });
    enableRankingGroups.value = false;
    enableEloHandicap.value = false;
    groupSize.value = 8;
    handicapRanges.value = defaultHandicapRanges.map((r) => ({ ...r }));
};

const save = () => {
    form.put(route("admin.rules.update"), {
        onError: () => toast.error("Erreur lors de l'enregistrement des règles."),
    });
};

const saveRule = () => {
    ruleForm.enable_ranking_groups = enableRankingGroups.value;
    ruleForm.enable_elo_handicap = enableEloHandicap.value;
    ruleForm.group_size = groupSize.value;
    ruleForm.handicap_parameters = handicapRanges.value.map((r) => ({
        min_gap: r.from,
        max_gap: r.to ?? 0,
        handicap: r.points,
    }));

    ruleForm.put(route("admin.rules.updateRule"), {
        onError: (errors) => {
            console.error('Validation errors:', errors);
            toast.error("Erreur lors de l'enregistrement des défis.");
        },
    });
};
</script>

<template>
    <Head title="Règles et défis - Admin" />

    <AdminLayout>
        <div class="p-8">
            <!-- Back link -->
            <Link
                :href="route('admin.dashboard')"
                class="inline-flex cursor-pointer items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground mb-5"
            >
                <ArrowLeft class="h-4 w-4" />
                Retour au dashboard
            </Link>

            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-100"
                    >
                        <Trophy class="h-6 w-6 text-red-500" />
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-foreground">
                            Règles et défis
                        </h1>
                        <p class="text-sm text-muted-foreground">
                            Configurer les points ELO et les règles de défis
                        </p>
                    </div>
                </div>
                <AdminClassPicker
                    :classes="classes"
                    :selected-class-id="selectedClassId"
                />
            </div>

            <!-- Points ELO section -->
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                    <zap class="h-4 w-4 text-amber-500" />
                    <h2 class="text-sm font-semibold text-foreground">
                        Points ELO
                    </h2>
                </div>

                <div class="rounded-2xl border border-border overflow-hidden">
                    <!-- Table header -->
                    <div
                        class="flex items-center px-5 py-3 border-b border-border bg-gray-50/70"
                    >
                        <span
                            class="flex-1 text-xs font-medium uppercase tracking-wider text-muted-foreground"
                            >Écart de classement</span
                        >
                        <span
                            class="w-44 text-right text-xs font-medium uppercase tracking-wider text-muted-foreground whitespace-nowrap"
                            >Points du vainqueur</span
                        >
                    </div>

                    <!-- Parameter rows -->
                    <div
                        v-for="(param, index) in parameters"
                        :key="param.id"
                        class="flex items-center px-5 py-4"
                        :class="
                            index < parameters.length - 1
                                ? 'border-b border-border'
                                : ''
                        "
                    >
                        <div class="flex items-center gap-3 flex-1">
                            <span
                                class="h-2.5 w-2.5 rounded-full shrink-0"
                                :class="getDotColor(param.winnerPoints)"
                            />
                            <div>
                                <p
                                    class="text-sm font-semibold text-foreground"
                                >
                                    {{ param.minDiff }} à {{ param.maxDiff }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ getDescription(param) }}
                                </p>
                            </div>
                        </div>
                        <div class="w-32 flex justify-end">
                            <input
                                v-model.number="
                                    form.parameters[index].winner_points
                                "
                                type="number"
                                step="0.1"
                                class="w-16 rounded-lg px-3 py-1.5 text-sm font-semibold text-center border border-border outline-none ring-0 focus:border-primary focus:ring-[3px] focus:ring-primary/20 transition-colors [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                :class="[
                                    getPointColor(
                                        form.parameters[index].winner_points,
                                    ),
                                    getPointBg(
                                        form.parameters[index].winner_points,
                                    ),
                                ]"
                            />
                        </div>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="flex items-center justify-end gap-3 mt-4">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl border border-border bg-white px-5 py-2.5 text-sm font-medium text-foreground hover:bg-gray-50 transition-colors cursor-pointer"
                        @click="resetDefaults"
                    >
                        <RotateCcw class="h-4 w-4" />
                        Réinitialiser
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-2.5 text-sm font-medium text-white hover:bg-primary/90 transition-colors cursor-pointer"
                        :disabled="form.processing"
                        @click="save"
                    >
                        <Save class="h-4 w-4" />
                        Sauvegarder les points
                    </button>
                </div>
            </div>

            <!-- Défis section -->
            <div>
                <p
                    class="text-xs font-medium uppercase tracking-wider text-muted-foreground mb-4"
                >
                    Défis
                </p>

                <div class="space-y-3">
                    <!-- Groupes de classement -->
                    <div
                        class="rounded-2xl border transition-colors"
                        :class="enableRankingGroups ? 'border-primary/30' : 'border-border'"
                    >
                        <div class="flex items-center gap-4 px-5 py-4">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100"
                            >
                                <Layers class="h-5 w-5 text-emerald-600" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-semibold text-foreground">
                                        Groupes de classement
                                    </p>
                                    <span
                                        v-if="enableRankingGroups"
                                        class="rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-medium text-primary"
                                    >
                                        Actif
                                    </span>
                                </div>
                                <p class="text-xs text-muted-foreground/60">
                                    Les joueurs ne peuvent défier que ceux de leur
                                    groupe. Les groupes sont formés par tranche de
                                    classement.
                                </p>
                            </div>
                            <Switch
                                v-model="enableRankingGroups"
                                class="h-7 w-12 shrink-0"
                            />
                        </div>

                        <!-- Panneau déroulant -->
                        <div v-if="enableRankingGroups" class="border-t border-border px-5 py-4 space-y-4">
                            <!-- Taille des groupes -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-foreground">Taille des groupes</p>
                                    <p class="text-xs text-muted-foreground/60">Nombre de joueurs par groupes</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        class="flex h-10 w-10 items-center justify-center rounded-lg border border-border bg-white hover:bg-gray-50 transition-colors cursor-pointer"
                                        @click="groupSize = Math.max(2, groupSize - 1)"
                                    >
                                        <Minus class="h-4 w-4 text-foreground" />
                                    </button>
                                    <input
                                        type="number"
                                        v-model.number="groupSize"
                                        min="2"
                                        class="h-10 w-14 rounded-xl border border-input bg-white text-center text-sm font-bold text-foreground outline-none focus:border-ring focus:ring-ring/50 focus:ring-[3px] [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                        @blur="groupSize = Math.max(2, groupSize || 2)"
                                    />
                                    <button
                                        type="button"
                                        class="flex h-10 w-10 items-center justify-center rounded-lg border border-border bg-white hover:bg-gray-50 transition-colors cursor-pointer"
                                        @click="groupSize++"
                                    >
                                        <Plus class="h-4 w-4 text-foreground" />
                                    </button>
                                </div>
                            </div>

                            <!-- Aperçu des groupes -->
                            <div class="rounded-xl bg-gray-50 p-4">
                                <p class="text-xs font-medium uppercase tracking-wider text-muted-foreground mb-3">
                                    Aperçu des groupes ({{ groups.length }} groupes)
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    <div
                                        v-for="group in groups"
                                        :key="group.name"
                                        class="rounded-lg border px-3 py-1.5"
                                        :class="[group.color.border, group.color.bg]"
                                    >
                                        <p class="text-xs font-semibold" :class="group.color.text">{{ group.name }}</p>
                                        <p class="text-xs" :class="group.color.text">Rang {{ group.from }}–{{ group.to }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Info exemple -->
                            <div class="flex gap-2.5 rounded-xl bg-gray-50 p-4">
                                <Info class="h-4 w-4 shrink-0 text-muted-foreground mt-0.5" />
                                <p class="text-xs text-muted-foreground leading-relaxed">
                                    <span class="font-semibold text-foreground">Exemple :</span> avec {{ totalPlayers }} joueurs et des groupes de {{ groupSize }}, le joueur classé 1<sup>er</sup> ne peut défier que les joueurs classés de 1 à {{ groupSize }}. Le joueur classé {{ groupSize + 1 }} ne peut défier que ceux de {{ groupSize + 1 }} à {{ groupSize * 2 }}.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Handicap d'ELO -->
                    <div
                        class="rounded-2xl border transition-colors"
                        :class="enableEloHandicap ? 'border-primary/30' : 'border-border'"
                    >
                        <div class="flex items-center gap-4 px-5 py-4">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100"
                            >
                                <Lightbulb class="h-5 w-5 text-amber-600" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-semibold text-foreground">
                                        Handicap de rang
                                    </p>
                                    <span
                                        v-if="enableEloHandicap"
                                        class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-600"
                                    >
                                        Actif
                                    </span>
                                </div>
                                <p class="text-xs text-muted-foreground/60">
                                    Si un joueur défie quelqu'un classé plus bas, il
                                    démarre le match avec un retard de points
                                    proportionnel à l'écart de ELO.
                                </p>
                            </div>
                            <Switch
                                v-model="enableEloHandicap"
                                class="h-7 w-12 shrink-0"
                            />
                        </div>

                        <!-- Panneau déroulant handicap -->
                        <div v-if="enableEloHandicap" class="border-t border-border px-5 py-4 space-y-4">
                            <!-- Aperçu du handicap -->
                            <div class="rounded-xl bg-gray-50 p-4">
                                <p class="text-xs font-medium uppercase tracking-wider text-muted-foreground mb-4">
                                    Aperçu du handicap
                                </p>
                                <div class="space-y-0 divide-y divide-border">
                                    <div
                                        v-for="(range, index) in handicapRanges"
                                        :key="index"
                                        class="flex items-center justify-between py-3.5"
                                    >
                                        <div class="flex items-center gap-2 text-sm text-foreground">
                                            Écart de
                                            <span class="rounded-md border border-border bg-white px-2.5 py-1 text-sm font-semibold">
                                                {{ range.to ? `${range.from} à ${range.to} ELO` : `${range.from}+ ELO` }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-1.5 mr-2">
                                            <input
                                                v-model.number="range.points"
                                                type="number"
                                                step="1"
                                                class="w-18 rounded-lg border px-3 py-2 text-base font-semibold text-center outline-none ring-0 focus:border-primary focus:ring-[3px] focus:ring-primary/20 transition-colors [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                                :class="range.points === 0
                                                    ? 'border-border bg-white text-foreground'
                                                    : 'border-amber-300 bg-amber-50/60 text-amber-600'"
                                            />
                                            <span class="text-sm text-muted-foreground">pts</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Info exemple -->
                            <div class="flex gap-2.5 rounded-xl bg-gray-50 p-4">
                                <Info class="h-4 w-4 shrink-0 text-muted-foreground mt-0.5" />
                                <p class="text-xs text-muted-foreground leading-relaxed">
                                    <span class="font-semibold text-foreground">Exemple :</span> le joueur classé 3<sup>e</sup> défie le 8<sup>e</sup> (écart = 5 ELO, tranche « 3 à 5 ELO »). Avec un handicap de <span class="font-semibold text-amber-600">3 pts</span>, le 3<sup>e</sup> démarre avec un score de 0 – 3.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="flex items-center justify-end gap-3 mt-4">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl border border-border bg-white px-5 py-2.5 text-sm font-medium text-foreground hover:bg-gray-50 transition-colors cursor-pointer"
                        @click="resetDefaults"
                    >
                        <RotateCcw class="h-4 w-4" />
                        Réinitialiser
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-2.5 text-sm font-medium text-white hover:bg-primary/90 transition-colors cursor-pointer"
                        :disabled="ruleForm.processing"
                        @click="saveRule"
                    >
                        <Save class="h-4 w-4" />
                        Sauvegarder les défis
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
