<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AdminClassPicker from '@/Components/admin/AdminClassPicker.vue';
import AdminActionCard from '@/Components/admin/AdminActionCard.vue';
import AdminRankingCard from '@/Components/admin/AdminRankingCard.vue';
import { Button } from '@/Components/ui/button';
import { Play, Users, Swords, Trophy, GraduationCap, Plus, ArrowRight } from 'lucide-vue-next';

const props = defineProps({
    classes: { type: Array, default: () => [] },
    selectedClassId: { type: Number, default: null },
    playerCount: { type: Number, default: 0 },
    matchCount: { type: Number, default: 0 },
    rankingPlayers: { type: Array, default: () => [] },
    period: { type: String, default: '30j' },
    hasActiveSession: { type: Boolean, default: false },
});

function startSession() {
    router.visit(route('admin.session'));
}
</script>

<template>
    <Head title="Dashboard Admin" />

    <AdminLayout>
        <!-- État normal : au moins un cours -->
        <div v-if="classes.length > 0" class="p-8 space-y-8">
            <!-- Gestion section -->
            <div>
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-2xl font-bold text-foreground">Gestion</h2>
                        <p class="mt-1 text-sm text-muted-foreground">Actions rapides administrateur</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <Button variant="outline" size="sm" class="rounded-xl gap-1.5 shadow-none" as-child>
                            <Link :href="route('admin.class.create')">
                                <Plus class="h-4 w-4" />
                                Nouveau cours
                            </Link>
                        </Button>
                        <AdminClassPicker :classes="classes" :selected-class-id="selectedClassId" />
                    </div>
                </div>

                <!-- Action cards -->
                <div class="grid grid-cols-4 gap-4">
                    <button
                        @click="startSession"
                        class="group relative flex cursor-pointer flex-col rounded-2xl border p-5 text-left transition-all"
                        :class="hasActiveSession ? 'border-primary bg-primary/5' : 'bg-white'"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl" :class="hasActiveSession ? 'bg-primary/20' : 'bg-primary/10'">
                                <Play class="h-5 w-5 text-primary" />
                            </div>
                            <span v-if="hasActiveSession" class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-600">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse" />
                                Active
                            </span>
                        </div>
                        <h3 class="mt-4 text-sm font-semibold" :class="hasActiveSession ? 'text-primary' : 'text-foreground'">{{ hasActiveSession ? 'Séance en cours' : 'Nouvelle séance' }}</h3>
                        <p class="mt-1 text-xs text-muted-foreground">{{ hasActiveSession ? 'Reprendre la session en cours' : 'Lancer une session de jeu pour le groupe' }}</p>
                    </button>
                    <AdminActionCard
                        title="Joueurs"
                        description="Gerer les joueurs et leurs profils"
                        :icon="Users"
                        :badge="playerCount"
                        :href="route('admin.players')"
                        icon-bg-class="bg-violet-100"
                        icon-color-class="text-violet-500"
                    />
                    <AdminActionCard
                        title="Matchs"
                        description="Historique et validation des matchs"
                        :icon="Swords"
                        :badge="matchCount"
                        :href="route('admin.matches')"
                        icon-bg-class="bg-orange-100"
                        icon-color-class="text-orange-500"
                    />
                    <AdminActionCard
                        title="Regles et defis"
                        description="Configurer les regles et creer des defis"
                        :icon="Trophy"
                        :href="route('admin.rules')"
                        icon-bg-class="bg-red-100"
                        icon-color-class="text-red-500"
                    />
                </div>
            </div>

            <!-- Classement section -->
            <AdminRankingCard :players="rankingPlayers" :player-count="playerCount" :period="period" />
        </div>

        <!-- État vide : aucun cours -->
        <div v-else class="flex min-h-[calc(100vh-4rem)] items-center justify-center p-8">
            <div class="w-full max-w-sm text-center">
                <div class="mb-6 flex justify-center">
                    <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-primary/10">
                        <GraduationCap class="h-10 w-10 text-primary" />
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-foreground">Aucun cours pour l'instant</h1>
                <p class="mt-2 mb-8 text-sm text-muted-foreground">
                    Créez votre premier cours pour commencer à gérer vos joueurs et séances.
                </p>
                <Button size="lg" class="rounded-xl w-full" as-child>
                    <Link :href="route('admin.class.create')">
                        Créer mon premier cours
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                </Button>
            </div>
        </div>
    </AdminLayout>
</template>
