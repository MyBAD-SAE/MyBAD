<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AdminClassPicker from '@/Components/admin/AdminClassPicker.vue';
import AdminActionCard from '@/Components/admin/AdminActionCard.vue';
import AdminRankingCard from '@/Components/admin/AdminRankingCard.vue';
import { Button } from '@/Components/ui/button';
import {
    AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent,
    AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle,
} from '@/Components/ui/alert-dialog';
import { Play, Users, Swords, Trophy, GraduationCap, Plus, ArrowRight, Trash2, TriangleAlert } from 'lucide-vue-next';

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

const selectedClass = computed(() =>
    props.classes.find((c) => c.id === props.selectedClassId) ?? null,
);

const showDeleteModal = ref(false);
const deleteConfirmation = ref('');
const canDelete = computed(() => deleteConfirmation.value === 'SUPPRIMER');

const deleteForm = useForm({});

function handleDeleteClass() {
    if (!props.selectedClassId) return;
    deleteForm.delete(route('admin.class.destroy', props.selectedClassId), {
        onFinish: () => {
            deleteConfirmation.value = '';
            showDeleteModal.value = false;
        },
    });
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

            <!-- Zone de danger -->
            <div>
                <div class="mb-5">
                    <h2 class="text-2xl font-bold text-foreground">Zone de danger</h2>
                    <p class="mt-1 text-sm text-muted-foreground">Actions irréversibles sur ce cours</p>
                </div>

                <div class="rounded-2xl border border-destructive/30 bg-destructive/5 p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-destructive/10">
                                <TriangleAlert class="h-5 w-5 text-destructive" />
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-foreground">
                                    Supprimer ce cours<span v-if="selectedClass"> — {{ selectedClass.name }}</span>
                                </h3>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    Toutes les données associées (joueurs, matchs, séances, ELO, règles) seront définitivement perdues.
                                </p>
                            </div>
                        </div>

                        <Button
                            variant="destructive"
                            size="sm"
                            class="shrink-0 rounded-xl gap-1.5"
                            :disabled="!selectedClassId"
                            @click="showDeleteModal = true"
                        >
                            <Trash2 class="h-4 w-4" />
                            Supprimer le cours
                        </Button>
                    </div>
                </div>
            </div>
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

        <!-- Modal suppression cours -->
        <AlertDialog :open="showDeleteModal" @update:open="(v) => { showDeleteModal = v; if (!v) deleteConfirmation = ''; }">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle class="flex items-center gap-2">
                        <TriangleAlert class="h-5 w-5 text-destructive" />
                        Supprimer ce cours ?
                    </AlertDialogTitle>
                    <AlertDialogDescription>
                        Cette action est <strong>irréversible</strong>. En supprimant le cours<span v-if="selectedClass"> « {{ selectedClass.name }} »</span>, vous perdrez définitivement toutes ses données.
                    </AlertDialogDescription>
                </AlertDialogHeader>

                <div class="rounded-xl border border-destructive/20 bg-destructive/5 px-4 py-3">
                    <ul class="list-disc space-y-1 pl-5 text-xs text-destructive">
                        <li>{{ playerCount }} joueur{{ playerCount > 1 ? 's' : '' }} associé{{ playerCount > 1 ? 's' : '' }}</li>
                        <li>{{ matchCount }} match{{ matchCount > 1 ? 's' : '' }} et historiques ELO</li>
                        <li>Toutes les séances et paramètres</li>
                        <li>Les règles et défis du cours</li>
                    </ul>
                </div>

                <div>
                    <label class="text-sm text-muted-foreground">
                        Pour confirmer, tapez
                        <span class="rounded-md border border-destructive/30 bg-destructive/10 px-1.5 py-0.5 text-xs font-semibold text-destructive">SUPPRIMER</span>
                    </label>
                    <input
                        v-model="deleteConfirmation"
                        type="text"
                        placeholder="SUPPRIMER"
                        class="mt-2 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm text-foreground shadow-none outline-none ring-0 placeholder:text-muted-foreground/50 focus:border-destructive/50 focus:ring-0"
                    />
                </div>

                <AlertDialogFooter>
                    <AlertDialogCancel>Annuler</AlertDialogCancel>
                    <AlertDialogAction
                        class="bg-destructive text-white hover:bg-destructive/90"
                        :disabled="!canDelete || deleteForm.processing"
                        @click="handleDeleteClass"
                    >
                        <Trash2 class="h-4 w-4" />
                        {{ deleteForm.processing ? 'Suppression...' : 'Supprimer' }}
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AdminLayout>
</template>
