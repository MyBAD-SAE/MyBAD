<script setup>
import { ref, computed, nextTick } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PlayerLayout from '@/Layouts/PlayerLayout.vue';
import BottomNavBar from '@/Components/BottomNavBar.vue';
import { Card, CardContent } from '@/Components/ui/card';
import { Separator } from '@/Components/ui/separator';
import {
    ArrowLeft,
    FileText,
    ClipboardList,
    Download,
    Trash2,
    TriangleAlert,
} from 'lucide-vue-next';

const props = defineProps({
    matchCount: { type: Number, default: 0 },
    eloHistoryCount: { type: Number, default: 0 },
    profileSize: { type: Number, default: 0 },
    eloSize: { type: Number, default: 0 },
    matchSize: { type: Number, default: 0 },
});

function formatSize(bytes) {
    if (bytes < 1024) return `${bytes} o`;
    return `~${(bytes / 1024).toFixed(1)} Ko`;
}

const dataItems = [
    { icon: FileText, title: 'Informations du profil', subtitle: 'Nom, email, préférences', size: formatSize(props.profileSize), color: 'text-primary', bg: 'bg-primary/10' },
    { icon: ClipboardList, title: 'Statistiques & ELO', subtitle: `${props.eloHistoryCount} entrée${props.eloHistoryCount > 1 ? 's' : ''} ELO`, size: formatSize(props.eloSize), color: 'text-violet-500', bg: 'bg-violet-50' },
    { icon: FileText, title: 'Historique des matchs', subtitle: `${props.matchCount} match${props.matchCount > 1 ? 's' : ''} enregistré${props.matchCount > 1 ? 's' : ''}`, size: formatSize(props.matchSize), color: 'text-amber-500', bg: 'bg-amber-50' },
];

// Delete modal
const showDeleteModal = ref(false);
const deleteConfirmation = ref('');

const canDelete = computed(() => deleteConfirmation.value === 'SUPPRIMER');

const deleteForm = useForm({
    confirmation: '',
});

function handleDelete() {
    deleteForm.confirmation = deleteConfirmation.value;
    deleteForm.delete(route('player.account.destroy'), {
        onFinish: () => {
            deleteConfirmation.value = '';
        },
    });
}

// Download modal
const showDownloadModal = ref(false);

// Success modal
const showSuccess = ref(false);
const progressWidth = ref(0);

function handleDownload() {
    showDownloadModal.value = false;

    const link = document.createElement('a');
    link.href = route('player.account.download');
    link.download = '';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    showSuccess.value = true;
    progressWidth.value = 0;

    nextTick(() => {
        requestAnimationFrame(() => {
            progressWidth.value = 100;
        });
    });

    setTimeout(() => {
        showSuccess.value = false;
    }, 2500);
}
</script>

<template>
    <Head title="Confidentialité" />

    <PlayerLayout>
        <div class="pb-20">
            <!-- Header -->
            <div class="relative flex items-center justify-center px-5 pt-6 pb-5">
                <Link :href="route('player.account.index')" class="absolute left-5 flex h-10 w-10 items-center justify-center rounded-2xl border border-border/50">
                    <ArrowLeft class="h-5 w-5 text-foreground" />
                </Link>
                <h1 class="text-lg font-bold text-foreground">Confidentialité</h1>
            </div>

            <!-- Mes données personnelles -->
            <Card class="mx-4 mt-2 gap-0 py-4">
                <CardContent class="px-4 py-0">
                    <span class="text-[11px] font-semibold uppercase tracking-widest text-muted-foreground/70">Mes données personnelles</span>

                    <p class="mt-2 text-sm leading-relaxed text-muted-foreground/70">
                        Vous pouvez télécharger une copie de toutes vos données personnelles au format JSON. Ce fichier contient votre profil, vos statistiques et votre historique de matchs.
                    </p>

                    <div class="mt-4 space-y-0">
                        <template v-for="(item, index) in dataItems" :key="item.title">
                            <div class="flex items-center gap-3 py-3">
                                <div :class="[item.bg, 'flex h-10 w-10 shrink-0 items-center justify-center rounded-full']">
                                    <component :is="item.icon" class="h-5 w-5" :class="item.color" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-foreground">{{ item.title }}</p>
                                    <p class="text-xs text-muted-foreground/60">{{ item.subtitle }}</p>
                                </div>
                                <span class="text-xs text-muted-foreground/60">{{ item.size }}</span>
                            </div>
                            <Separator v-if="index < dataItems.length - 1" />
                        </template>
                    </div>

                    <!-- Bouton télécharger -->
                    <button
                        class="mt-4 flex w-full items-center justify-center gap-2 rounded-2xl bg-primary py-3.5 text-sm font-semibold text-white"
                        @click="showDownloadModal = true"
                    >
                        <Download class="h-4 w-4" />
                        Télécharger mes données
                    </button>
                </CardContent>
            </Card>

            <!-- Zone de danger -->
            <Card class="mx-4 mt-4 gap-0 py-4">
                <CardContent class="px-4 py-0">
                    <span class="text-[11px] font-semibold uppercase tracking-widest text-red-500">Zone de danger</span>

                    <button class="mt-3 flex w-full items-center gap-3" @click="showDeleteModal = true">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-50">
                            <Trash2 class="h-5 w-5 text-red-500" />
                        </div>
                        <div class="flex-1 text-left">
                            <p class="text-sm font-medium text-red-500">Supprimer mon compte</p>
                            <p class="text-xs text-muted-foreground/60">Action irréversible, toutes les données seront perdues</p>
                        </div>
                    </button>
                </CardContent>
            </Card>
        </div>

        <!-- Modal téléchargement -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showDownloadModal" class="fixed inset-0 z-50 flex items-end justify-center sm:items-center">
                    <div class="absolute inset-0 bg-black/20" @click="showDownloadModal = false" />

                    <div class="relative mx-4 mb-4 w-full max-w-md rounded-3xl bg-white px-6 py-7 shadow-xl">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary/10">
                                <Download class="h-5 w-5 text-primary" />
                            </div>
                            <h2 class="text-base font-bold text-foreground">Télécharger mes données</h2>
                        </div>

                        <p class="mt-4 text-sm leading-relaxed text-muted-foreground">
                            Un fichier JSON contenant toutes vos données personnelles (profil, statistiques, etc) sera téléchargé sur votre appareil.
                        </p>

                        <div class="mt-5 flex gap-3">
                            <button
                                class="flex-1 rounded-2xl border border-border/50 py-2.5 text-sm font-medium text-foreground"
                                @click="showDownloadModal = false"
                            >
                                Annuler
                            </button>
                            <button
                                class="flex-1 rounded-2xl bg-primary py-2.5 text-sm font-semibold text-white"
                                @click="handleDownload"
                            >
                                Télécharger
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Modal succès téléchargement -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showSuccess" class="fixed inset-0 z-50 flex items-center justify-center">
                    <div class="absolute inset-0 bg-black/20 backdrop-blur-sm" />

                    <div class="relative mx-8 w-full max-w-sm rounded-3xl bg-white px-8 py-10 shadow-xl">
                        <div class="flex flex-col items-center text-center">
                            <div class="relative">
                                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary">
                                    <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <svg class="absolute -right-2 -top-2 h-6 w-6 text-primary/60" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2l1.5 4.5L18 8l-4.5 1.5L12 14l-1.5-4.5L6 8l4.5-1.5z" />
                                </svg>
                                <svg class="absolute -bottom-1 -left-3 h-5 w-5 text-primary/40" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2l1.5 4.5L18 8l-4.5 1.5L12 14l-1.5-4.5L6 8l4.5-1.5z" />
                                </svg>
                            </div>

                            <h2 class="mt-5 text-lg font-bold text-foreground">Données téléchargées</h2>
                            <p class="mt-1 text-sm text-muted-foreground">Votre fichier a été enregistré.</p>

                            <div class="mt-5 h-1.5 w-full overflow-hidden rounded-full bg-muted">
                                <div
                                    class="h-full rounded-full bg-primary transition-all duration-[2300ms] ease-linear"
                                    :style="{ width: progressWidth + '%' }"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Modal suppression -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-end justify-center sm:items-center">
                    <!-- Backdrop -->
                    <div class="absolute inset-0 bg-black/20" @click="showDeleteModal = false" />

                    <!-- Modal -->
                    <div class="relative mx-4 mb-4 w-full max-w-md rounded-3xl bg-white px-6 py-7 shadow-xl">
                        <!-- Header -->
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-50">
                                <TriangleAlert class="h-5 w-5 text-red-500" />
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-foreground">Supprimer mon compte</h2>
                                <p class="text-sm text-muted-foreground">Cette action est irréversible</p>
                            </div>
                        </div>

                        <!-- Warning box -->
                        <div class="mt-5 rounded-2xl bg-red-50/70 px-5 py-4">
                            <p class="text-xs leading-relaxed text-red-300">En supprimant votre compte, vous perdrez définitivement :</p>
                            <ul class="mt-3 list-disc space-y-2 pl-5 text-xs text-red-300">
                                <li>Votre profil et vos informations personnelles</li>
                                <li>Votre historique de {{ matchCount }} match{{ matchCount > 1 ? 's' : '' }}</li>
                                <li>Votre score ELO et vos statistiques</li>
                                <li>Votre position au classement</li>
                            </ul>
                        </div>

                        <!-- Confirmation input -->
                        <div class="mt-5">
                            <p class="whitespace-nowrap text-sm text-muted-foreground">
                                Pour confirmer, tapez
                                <span class="rounded-md border border-red-200 bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-500">SUPPRIMER</span>
                                ci-dessous :
                            </p>
                            <input
                                v-model="deleteConfirmation"
                                type="text"
                                placeholder="SUPPRIMER"
                                class="mt-3 w-full rounded-2xl border border-red-200 bg-transparent px-4 py-3 text-base text-red-500 shadow-none outline-none ring-0 placeholder:text-red-200 focus:border-red-400 focus:ring-0"
                            />
                        </div>

                        <!-- Buttons -->
                        <div class="mt-5 flex gap-3">
                            <button
                                class="flex-1 rounded-2xl border border-border/50 py-2.5 text-sm font-medium text-foreground"
                                @click="showDeleteModal = false; deleteConfirmation = ''"
                            >
                                Annuler
                            </button>
                            <button
                                class="flex-1 rounded-2xl py-2.5 text-sm font-semibold text-white transition-colors"
                                :class="canDelete && !deleteForm.processing ? 'bg-red-500' : 'bg-red-300 cursor-not-allowed'"
                                :disabled="!canDelete || deleteForm.processing"
                                @click="handleDelete"
                            >
                                {{ deleteForm.processing ? 'Suppression...' : 'Supprimer' }}
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <BottomNavBar active="profil" />
    </PlayerLayout>
</template>
