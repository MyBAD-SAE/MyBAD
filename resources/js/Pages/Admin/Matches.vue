<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Input } from '@/Components/ui/input';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Button } from '@/Components/ui/button';
import {
    ArrowLeft,
    ChevronLeft,
    ChevronRight,
    Swords,
    Search,
    ChevronDown,
    Calendar,
    Pencil,
    Trash2,
    AlertTriangle,
    PenLine,
    Save,
} from 'lucide-vue-next';

const props = defineProps({
    sessions: { type: Array, default: () => [] },
    totalMatchCount: { type: Number, default: 0 },
    topMatchesPlayer: { type: String, default: null },
    topWinsPlayer: { type: String, default: null },
    selectedClass: { type: Object, default: null },
});

const search = ref('');
const activeSession = ref('all');
const sortOpen = ref(false);
const sortBy = ref('recent');
const sortContainer = ref(null);

const sortOptions = [
    { value: 'recent', label: 'Plus récent' },
    { value: 'oldest', label: 'Plus ancien' },
];

function onSortClickOutside(e) {
    if (sortContainer.value && !sortContainer.value.contains(e.target)) {
        sortOpen.value = false;
    }
}

onMounted(() => document.addEventListener('click', onSortClickOutside));
onBeforeUnmount(() => document.removeEventListener('click', onSortClickOutside));

const allMatches = computed(() => {
    return props.sessions.flatMap(s => s.matches);
});

const searchFilteredSessions = computed(() => {
    if (!search.value.trim()) return props.sessions;
    const q = search.value.toLowerCase();
    return props.sessions.map(s => ({
        ...s,
        matches: s.matches.filter(m =>
            m.player1.name.toLowerCase().includes(q) ||
            m.player2.name.toLowerCase().includes(q)
        ),
    })).filter(s => s.matches.length > 0);
});

const filteredTotalMatchCount = computed(() => {
    return searchFilteredSessions.value.reduce((sum, s) => sum + s.matches.length, 0);
});


const filteredSessions = computed(() => {
    let sessions = searchFilteredSessions.value;

    if (activeSession.value !== 'all') {
        sessions = sessions.filter(s => s.id === activeSession.value);
    }

    if (sortBy.value === 'oldest') {
        sessions = [...sessions].reverse();
    }

    return sessions;
});

// Delete modal
const showDeleteModal = ref(false);
const matchToDelete = ref(null);
const deleteForm = useForm({});

const openDeleteModal = (match) => {
    matchToDelete.value = match;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    matchToDelete.value = null;
};

const confirmDelete = () => {
    if (!matchToDelete.value) return;
    deleteForm.delete(route('admin.matches.destroy', matchToDelete.value.id), {
        onSuccess: () => closeDeleteModal(),
        onError: () => toast.error('Erreur lors de la suppression du match.'),
    });
};

// Edit modal
const showEditModal = ref(false);
const matchToEdit = ref(null);
const editForm = useForm({
    score1: 0,
    score2: 0,
    player1_id: null,
    player2_id: null,
});

const openEditModal = (match) => {
    matchToEdit.value = match;
    editForm.score1 = match.player1.score;
    editForm.score2 = match.player2.score;
    editForm.player1_id = match.player1.id;
    editForm.player2_id = match.player2.id;
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    matchToEdit.value = null;
};

const confirmEdit = () => {
    if (!matchToEdit.value) return;
    editForm.put(route('admin.matches.update', matchToEdit.value.id), {
        onSuccess: () => closeEditModal(),
        onError: () => toast.error('Erreur lors de la modification du match.'),
    });
};

const tabsScroll = ref(null);
const canScrollRight = ref(false);
const canScrollLeft = ref(false);

const checkScroll = () => {
    const el = tabsScroll.value;
    if (!el) return;
    canScrollRight.value = el.scrollLeft + el.clientWidth < el.scrollWidth - 4;
    canScrollLeft.value = el.scrollLeft > 4;
};

const scrollRight = () => {
    tabsScroll.value?.scrollBy({ left: 200, behavior: 'smooth' });
};

const scrollLeft = () => {
    tabsScroll.value?.scrollBy({ left: -200, behavior: 'smooth' });
};

onMounted(() => {
    nextTick(() => checkScroll());
});

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(p => p.charAt(0)).join('').toUpperCase();
};
</script>

<template>
    <Head title="Matchs - Admin" />

    <AdminLayout>
        <div class="p-8">
            <!-- Back link -->
            <Link :href="route('admin.dashboard')" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground transition-colors mb-6">
                <ArrowLeft class="h-4 w-4" />
                Retour au dashboard
            </Link>

            <!-- Header card -->
            <div class="rounded-2xl border border-border p-6 mb-6">
                <div class="flex items-center gap-4 mb-5">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100">
                        <Swords class="h-6 w-6 text-orange-500" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl font-bold text-foreground">Matchs</h1>
                            <span class="inline-flex h-6 min-w-6 items-center justify-center rounded-full bg-gray-100 px-2 text-xs font-semibold text-foreground">{{ totalMatchCount }}</span>
                        </div>
                        <p class="text-sm text-muted-foreground">{{ selectedClass ? `${selectedClass.name} | ${selectedClass.school_year}` : 'Historique complet des matchs' }}</p>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="rounded-xl border border-border py-4 text-center">
                        <p class="text-xl font-bold text-foreground">{{ totalMatchCount }}</p>
                        <p class="text-xs text-muted-foreground">Total matchs</p>
                    </div>
                    <div class="rounded-xl border border-border py-4 text-center">
                        <p class="text-xl font-bold text-foreground">{{ topMatchesPlayer ?? '-' }}</p>
                        <p class="text-xs text-muted-foreground">Joueur avec le plus de matchs</p>
                    </div>
                    <div class="rounded-xl border border-border py-4 text-center">
                        <p class="text-xl font-bold text-foreground">{{ topWinsPlayer ?? '-' }}</p>
                        <p class="text-xs text-muted-foreground">Joueur avec le plus de victoire</p>
                    </div>
                </div>
            </div>

            <!-- Session tabs (carousel) -->
            <div class="flex items-center gap-3 mb-4">
                <button
                    v-if="canScrollLeft"
                    class="flex shrink-0 h-10 w-10 items-center justify-center rounded-2xl border border-gray-200 bg-white transition-colors hover:bg-gray-50 cursor-pointer"
                    @click="scrollLeft"
                >
                    <ChevronLeft class="h-4 w-4 text-foreground" />
                </button>
                <div
                    ref="tabsScroll"
                    class="flex-1 overflow-x-auto"
                    style="-ms-overflow-style: none; scrollbar-width: none;"
                    @scroll="checkScroll"
                >
                    <div class="flex items-center gap-3">
                        <button
                            class="inline-flex shrink-0 items-center gap-2 rounded-2xl px-5 py-2.5 text-sm font-medium transition-colors cursor-pointer"
                            :class="activeSession === 'all' ? 'bg-gray-900 text-white' : 'border border-gray-200 bg-white text-muted-foreground hover:bg-gray-50'"
                            @click="activeSession = 'all'"
                        >
                            Tout
                            <span class="text-sm" :class="activeSession === 'all' ? 'text-white/60' : 'text-muted-foreground/60'">{{ filteredTotalMatchCount }}</span>
                        </button>
                        <button
                            v-for="session in searchFilteredSessions"
                            :key="session.id"
                            class="inline-flex shrink-0 items-center gap-2 rounded-2xl px-5 py-2.5 text-sm font-medium transition-colors cursor-pointer"
                            :class="activeSession === session.id ? 'bg-gray-900 text-white' : 'border border-gray-200 bg-white text-muted-foreground hover:bg-gray-50'"
                            @click="activeSession = session.id"
                        >
                            {{ session.label }}
                            <span class="text-sm" :class="activeSession === session.id ? 'text-white/60' : 'text-muted-foreground/60'">{{ session.matches.length }}</span>
                        </button>
                    </div>
                </div>
                <button
                    v-if="canScrollRight"
                    class="flex shrink-0 h-10 w-10 items-center justify-center rounded-2xl border border-gray-200 bg-white transition-colors hover:bg-gray-50 cursor-pointer"
                    @click="scrollRight"
                >
                    <ChevronRight class="h-4 w-4 text-foreground" />
                </button>
            </div>

            <!-- Search + Sort -->
            <div class="flex items-center gap-4 mb-4">
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                    <Input
                        v-model="search"
                        type="text"
                        placeholder="Rechercher un joueur..."
                        class="pl-9 w-full text-sm placeholder:text-gray-400 shadow-none focus-visible:ring-1 focus-visible:ring-primary"
                    />
                </div>
                <div ref="sortContainer" class="relative">
                    <button
                        class="inline-flex h-9 items-center gap-2 rounded-md border border-input bg-white px-4 text-sm text-foreground transition-colors hover:bg-gray-50 cursor-pointer"
                        @click="sortOpen = !sortOpen"
                    >
                        {{ sortOptions.find(o => o.value === sortBy)?.label }}
                        <ChevronDown class="h-4 w-4 text-muted-foreground" />
                    </button>
                    <div v-if="sortOpen" class="absolute right-0 top-full z-50 mt-1 min-w-[150px] rounded-xl border border-border bg-white py-1 shadow-lg">
                        <button
                            v-for="opt in sortOptions"
                            :key="opt.value"
                            class="flex w-full px-4 py-2 text-left text-sm transition-colors hover:bg-gray-50 cursor-pointer"
                            :class="sortBy === opt.value && 'text-primary font-medium'"
                            @click="sortBy = opt.value; sortOpen = false"
                        >
                            {{ opt.label }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Match list grouped by session -->
            <div class="space-y-6">
                <div v-for="session in filteredSessions" :key="session.id" class="rounded-2xl border border-border overflow-hidden">
                    <!-- Session header -->
                    <div class="flex items-center justify-between bg-gray-50/70 px-5 py-4 border-b border-border">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-100">
                                <Calendar class="h-5 w-5 text-orange-500" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-foreground">{{ session.session_name }}</p>
                                <p class="text-xs text-muted-foreground">{{ session.formatted_date }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-muted-foreground border border-border rounded-full bg-white px-3 py-1">{{ session.matches.length }} matchs</span>
                    </div>

                    <!-- Matches -->
                    <div class="divide-y">
                        <div
                            v-for="match in session.matches"
                            :key="match.id"
                            class="flex items-center px-5 py-4"
                        >
                            <!-- Number -->
                            <span class="w-6 text-center text-sm text-muted-foreground shrink-0">{{ match.number }}</span>

                            <!-- Player 1 -->
                            <div class="flex items-center gap-3 flex-1 min-w-0 ml-3">
                                <Avatar class="h-10 w-10 shrink-0">
                                    <AvatarImage v-if="match.player1.avatar" :src="match.player1.avatar" />
                                    <AvatarFallback class="text-xs">{{ getInitials(match.player1.name) }}</AvatarFallback>
                                </Avatar>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-foreground truncate">{{ match.player1.name }}</p>
                                    <p v-if="match.player1.won" class="text-xs font-medium text-primary">Victoire</p>
                                    <p v-else class="text-xs font-medium text-destructive">Défaite</p>
                                </div>
                            </div>

                            <!-- Score -->
                            <div class="flex items-center gap-2 shrink-0 mx-4">
                                <span
                                    class="inline-flex h-9 w-10 items-center justify-center rounded-lg text-sm font-bold"
                                    :class="match.player1.won ? 'bg-primary/10 text-primary' : 'bg-gray-100 text-foreground'"
                                >
                                    {{ match.player1.score }}
                                </span>
                                <span class="text-xs text-muted-foreground">vs</span>
                                <span
                                    class="inline-flex h-9 w-10 items-center justify-center rounded-lg text-sm font-bold"
                                    :class="match.player2.won ? 'bg-primary/10 text-primary' : 'bg-gray-100 text-foreground'"
                                >
                                    {{ match.player2.score }}
                                </span>
                            </div>

                            <!-- Player 2 -->
                            <div class="flex items-center gap-3 flex-1 min-w-0 justify-end">
                                <div class="min-w-0 text-right">
                                    <p class="text-sm font-medium text-foreground truncate">{{ match.player2.name }}</p>
                                    <p v-if="match.player2.won" class="text-xs font-medium text-primary">Victoire</p>
                                    <p v-else class="text-xs font-medium text-destructive">Défaite</p>
                                </div>
                                <Avatar class="h-10 w-10 shrink-0">
                                    <AvatarImage v-if="match.player2.avatar" :src="match.player2.avatar" />
                                    <AvatarFallback class="text-xs">{{ getInitials(match.player2.name) }}</AvatarFallback>
                                </Avatar>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2 shrink-0 ml-4">
                                <button
                                    class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-lg transition-colors hover:bg-gray-100"
                                    @click="openEditModal(match)"
                                >
                                    <Pencil class="h-4 w-4 text-muted-foreground" />
                                </button>
                                <button
                                    class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-lg transition-colors hover:bg-red-50"
                                    @click="openDeleteModal(match)"
                                >
                                    <Trash2 class="h-4 w-4 text-destructive" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty state -->
                <div v-if="filteredSessions.length === 0" class="py-12 text-center">
                    <p class="text-sm text-muted-foreground">Aucun match trouvé.</p>
                </div>
            </div>
        </div>
        <!-- Edit match modal -->
        <Teleport to="body">
            <Transition name="fade">
                <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center">
                    <div class="absolute inset-0 bg-black/40" @click="closeEditModal" />

                    <div class="relative z-10 mx-4 w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                        <!-- Icon -->
                        <div class="mb-4 flex justify-center">
                            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-primary/10">
                                <PenLine class="h-7 w-7 text-primary" />
                            </div>
                        </div>

                        <!-- Title -->
                        <h3 class="mb-5 text-center text-lg font-bold text-foreground">
                            Modifier ce match ?
                        </h3>

                        <!-- Match scores card -->
                        <div v-if="matchToEdit" class="mb-5 grid grid-cols-[1fr_auto_1fr] items-center gap-3 overflow-hidden rounded-xl bg-gray-50 px-6 py-5">
                            <!-- Player 1 -->
                            <div class="flex items-center gap-2.5 justify-self-start">
                                <Avatar class="h-10 w-10 shrink-0">
                                    <AvatarImage v-if="matchToEdit.player1.avatar" :src="matchToEdit.player1.avatar" />
                                    <AvatarFallback class="text-xs">{{ getInitials(matchToEdit.player1.name) }}</AvatarFallback>
                                </Avatar>
                                <span class="text-sm font-semibold text-foreground truncate">{{ matchToEdit.player1.name.split(' ')[0] }}</span>
                            </div>

                            <!-- Score inputs -->
                            <div class="flex items-center gap-3">
                                <input
                                    v-model.number="editForm.score1"
                                    type="number"
                                    min="0"
                                    max="30"
                                    class="h-11 w-14 appearance-none rounded-lg border border-border bg-white text-center text-base font-bold text-foreground shadow-none outline-none [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none focus:ring-1 focus:ring-primary"
                                />
                                <span class="text-sm text-muted-foreground">vs</span>
                                <input
                                    v-model.number="editForm.score2"
                                    type="number"
                                    min="0"
                                    max="30"
                                    class="h-11 w-14 appearance-none rounded-lg border border-border bg-white text-center text-base font-bold text-foreground shadow-none outline-none [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none focus:ring-1 focus:ring-primary"
                                />
                            </div>

                            <!-- Player 2 -->
                            <div class="flex items-center gap-2.5 justify-self-end">
                                <span class="text-sm font-semibold text-foreground truncate">{{ matchToEdit.player2.name.split(' ')[0] }}</span>
                                <Avatar class="h-10 w-10 shrink-0">
                                    <AvatarImage v-if="matchToEdit.player2.avatar" :src="matchToEdit.player2.avatar" />
                                    <AvatarFallback class="text-xs">{{ getInitials(matchToEdit.player2.name) }}</AvatarFallback>
                                </Avatar>
                            </div>
                        </div>

                        <!-- Info text -->
                        <p class="mb-6 text-center text-sm text-muted-foreground">
                            Les ELO des joueurs seront recalculés automatiquement.
                        </p>

                        <!-- Actions -->
                        <div class="grid grid-cols-2 gap-3">
                            <Button
                                variant="outline"
                                size="lg"
                                class="rounded-xl"
                                @click="closeEditModal"
                            >
                                Annuler
                            </Button>
                            <Button
                                size="lg"
                                class="rounded-xl"
                                :disabled="editForm.processing"
                                @click="confirmEdit"
                            >
                                <Save class="h-4 w-4" />
                                Sauvegarder
                            </Button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Delete confirmation modal -->
        <Teleport to="body">
            <Transition name="fade">
                <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center">
                    <div class="absolute inset-0 bg-black/40" @click="closeDeleteModal" />

                    <div class="relative z-10 mx-4 w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
                        <!-- Warning icon -->
                        <div class="mb-4 flex justify-center">
                            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-50">
                                <AlertTriangle class="h-7 w-7 text-destructive" />
                            </div>
                        </div>

                        <!-- Title -->
                        <h3 class="mb-4 text-center text-lg font-bold text-foreground">
                            Supprimer ce match ?
                        </h3>

                        <!-- Match info card -->
                        <div v-if="matchToDelete" class="mb-4 flex items-center justify-between rounded-xl bg-gray-50 px-4 py-3">
                            <div class="flex items-center gap-2">
                                <Avatar class="h-9 w-9 shrink-0">
                                    <AvatarImage v-if="matchToDelete.player1.avatar" :src="matchToDelete.player1.avatar" />
                                    <AvatarFallback class="text-xs">{{ getInitials(matchToDelete.player1.name) }}</AvatarFallback>
                                </Avatar>
                                <span class="text-sm font-medium text-foreground">{{ matchToDelete.player1.name.split(' ')[0] }}</span>
                            </div>
                            <span class="text-xs text-muted-foreground">vs</span>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-foreground">{{ matchToDelete.player2.name.split(' ')[0] }}</span>
                                <Avatar class="h-9 w-9 shrink-0">
                                    <AvatarImage v-if="matchToDelete.player2.avatar" :src="matchToDelete.player2.avatar" />
                                    <AvatarFallback class="text-xs">{{ getInitials(matchToDelete.player2.name) }}</AvatarFallback>
                                </Avatar>
                            </div>
                        </div>

                        <!-- Warning text -->
                        <p class="mb-6 text-center text-sm text-muted-foreground">
                            Les ELO des joueurs seront recalculés automatiquement.
                        </p>

                        <!-- Actions -->
                        <div class="grid grid-cols-2 gap-3">
                            <Button
                                variant="outline"
                                size="lg"
                                class="rounded-xl"
                                @click="closeDeleteModal"
                            >
                                Annuler
                            </Button>
                            <Button
                                variant="destructive"
                                size="lg"
                                class="rounded-xl"
                                :disabled="deleteForm.processing"
                                @click="confirmDelete"
                            >
                                <Trash2 class="h-4 w-4" />
                                Supprimer
                            </Button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </AdminLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
