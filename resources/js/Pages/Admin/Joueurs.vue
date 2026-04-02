<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Input } from '@/Components/ui/input';
import { Button } from '@/Components/ui/button';
import { Switch } from '@/Components/ui/switch';
import { Label } from '@/Components/ui/label';
import {
    ArrowLeft,
    Users,
    Search,
    ChevronDown,
    TrendingUp,
    TrendingDown,
    Minus,
    Pencil,
    Trash2,
    Plus,
    AlertTriangle,
    Crown,
    Save,
    ShieldOff,
    UserPlus,
} from 'lucide-vue-next';

const props = defineProps({
    players: { type: Array, default: () => [] },
    playerCount: { type: Number, default: 0 },
    activePlayerCount: { type: Number, default: 0 },
    matchCount: { type: Number, default: 0 },
    averageElo: { type: Number, default: 0 },
    classes: { type: Array, default: () => [] },
    selectedClassId: { type: Number, default: null },
});

const search = ref('');
const sortBy = ref('elo');
const sortOpen = ref(false);

// Delete modal
const showDeleteModal = ref(false);
const playerToDelete = ref(null);
const deleteForm = useForm({});

const openDeleteModal = (player) => {
    playerToDelete.value = player;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    playerToDelete.value = null;
};

const confirmDelete = () => {
    if (!playerToDelete.value) return;
    deleteForm.delete(route('admin.joueurs.destroy', playerToDelete.value.participantId), {
        onSuccess: () => closeDeleteModal(),
    });
};

// Edit modal
const showEditModal = ref(false);
const playerToEdit = ref(null);
const editIsActive = ref(false);
const editForm = useForm({
    elo: 0,
    is_active: true,
    make_admin: false,
    user_id: null,
});

const openEditModal = (player) => {
    playerToEdit.value = player;
    editForm.elo = player.elo;
    editIsActive.value = Boolean(player.isActive);
    editForm.is_active = Boolean(player.isActive);
    editForm.make_admin = player.isAdmin;
    editForm.user_id = player.userId;
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    playerToEdit.value = null;
};

const confirmEdit = () => {
    if (!playerToEdit.value) return;
    editForm.is_active = editIsActive.value;
    editForm.put(route('admin.joueurs.update', playerToEdit.value.participantId), {
        onSuccess: () => closeEditModal(),
    });
};

// Add player modal
const showAddModal = ref(false);
const addForm = useForm({
    code: '',
    elo: 100,
});

const openAddModal = () => {
    addForm.reset();
    addForm.clearErrors();
    showAddModal.value = true;
};

const closeAddModal = () => {
    showAddModal.value = false;
};

const confirmAdd = () => {
    addForm.post(route('admin.joueurs.store'), {
        onSuccess: () => closeAddModal(),
    });
};

const formatCode = (e) => {
    const raw = addForm.code.replace(/\D/g, '').slice(0, 6);
    addForm.code = raw;
};

const sortOptions = [
    { value: 'elo', label: 'ELO' },
    { value: 'name', label: 'Nom' },
    { value: 'winRate', label: 'Win rate' },
];

const filteredPlayers = computed(() => {
    let list = [...props.players];

    if (search.value) {
        const q = search.value.toLowerCase();
        list = list.filter(p => p.name.toLowerCase().includes(q));
    }

    if (sortBy.value === 'name') {
        list.sort((a, b) => a.name.localeCompare(b.name));
    } else if (sortBy.value === 'winRate') {
        list.sort((a, b) => b.winRate - a.winRate);
    }

    return list;
});

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(p => p.charAt(0)).join('').toUpperCase();
};

const getWinRateDot = (winRate) => {
    if (winRate >= 70) return 'bg-primary';
    if (winRate >= 50) return 'bg-orange-400';
    return 'bg-red-400';
};
</script>

<template>
    <Head title="Joueurs - Admin" />

    <AdminLayout>
        <div class="p-8">
            <!-- Back link -->
            <Link :href="route('admin.dashboard')" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground transition-colors mb-6">
                <ArrowLeft class="h-4 w-4" />
                Retour au dashboard
            </Link>

            <!-- Header card -->
            <div class="rounded-2xl border border-border p-6 mb-6">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-100">
                            <Users class="h-6 w-6 text-violet-500" />
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="text-xl font-bold text-foreground">Joueurs</h1>
                                <span class="inline-flex h-6 min-w-6 items-center justify-center rounded-full bg-gray-100 px-2 text-xs font-semibold text-foreground">{{ playerCount }}</span>
                            </div>
                            <p class="text-sm text-muted-foreground">Gérer les joueurs, ELO et statistiques</p>
                        </div>
                    </div>
                    <button
                        class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-primary/90"
                        @click="openAddModal"
                    >
                        <Plus class="h-4 w-4" />
                        Ajouter un joueur
                    </button>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="rounded-xl border border-border py-4 text-center">
                        <p class="text-xl font-bold text-foreground">{{ averageElo }}</p>
                        <p class="text-xs text-muted-foreground">ELO moyen</p>
                    </div>
                    <div class="rounded-xl border border-border py-4 text-center">
                        <p class="text-xl font-bold text-foreground">{{ matchCount }}</p>
                        <p class="text-xs text-muted-foreground">Matchs joués</p>
                    </div>
                    <div class="rounded-xl border border-border py-4 text-center">
                        <p class="text-xl font-bold text-foreground">{{ activePlayerCount }}</p>
                        <p class="text-xs text-muted-foreground">Joueurs actifs</p>
                    </div>
                </div>
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
                <div class="relative">
                    <button
                        class="inline-flex h-9 items-center gap-2 rounded-md border border-input bg-white px-4 text-sm text-foreground transition-colors hover:bg-gray-50 cursor-pointer"
                        @click="sortOpen = !sortOpen"
                    >
                        Trier : {{ sortOptions.find(o => o.value === sortBy)?.label }}
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

            <!-- Player list -->
            <div class="rounded-2xl border border-border divide-y">
                <div
                    v-for="player in filteredPlayers"
                    :key="player.rank"
                    class="flex items-center gap-4 px-5 py-4"
                >
                    <!-- Rank -->
                    <span class="w-6 text-center text-sm font-medium text-muted-foreground">{{ player.rank }}</span>

                    <!-- Avatar -->
                    <Avatar class="h-11 w-11 shrink-0">
                        <AvatarImage v-if="player.avatar" :src="player.avatar" />
                        <AvatarFallback class="text-xs">{{ getInitials(player.name) }}</AvatarFallback>
                    </Avatar>

                    <!-- Info -->
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-foreground">{{ player.name }}</span>
                            <span v-if="player.isAdmin" class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-1.5 py-0.5 text-xs font-semibold text-amber-600">
                                <Crown class="h-3 w-3" />
                                Admin
                            </span>
                            <span v-if="!player.isActive" class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-1.5 py-0.5 text-xs font-semibold text-gray-500">
                                Désactivé
                            </span>
                            <template v-if="player.trend > 0">
                                <TrendingUp class="h-3.5 w-3.5 text-primary" />
                                <span class="text-xs font-medium text-primary">+{{ player.trend }}</span>
                            </template>
                            <template v-else-if="player.trend < 0">
                                <TrendingDown class="h-3.5 w-3.5 text-destructive" />
                                <span class="text-xs font-medium text-destructive">{{ player.trend }}</span>
                            </template>
                            <template v-else>
                                <Minus class="h-3.5 w-3.5 text-muted-foreground" />
                                <span class="text-xs text-muted-foreground">0</span>
                            </template>
                        </div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-xs">
                                <span class="font-medium text-primary">{{ player.wins }}V</span>
                                <span class="text-muted-foreground"> · </span>
                                <span class="font-medium text-destructive">{{ player.losses }}D</span>
                            </span>
                            <span class="h-2 w-2 rounded-full" :class="getWinRateDot(player.winRate)" />
                            <span class="text-xs text-muted-foreground">{{ player.winRate }}%</span>
                        </div>
                    </div>

                    <!-- ELO -->
                    <div class="text-right shrink-0">
                        <p class="text-lg font-bold text-foreground">{{ player.elo }}</p>
                        <p class="text-xs text-muted-foreground">ELO</p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 shrink-0">
                        <button
                            class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-lg transition-colors hover:bg-gray-100"
                            @click="openEditModal(player)"
                        >
                            <Pencil class="h-4 w-4 text-muted-foreground" />
                        </button>
                        <button
                            class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-lg transition-colors hover:bg-red-50"
                            @click="openDeleteModal(player)"
                        >
                            <Trash2 class="h-4 w-4 text-destructive" />
                        </button>
                    </div>
                </div>

                <!-- Empty state -->
                <div v-if="filteredPlayers.length === 0" class="py-12 text-center">
                    <p class="text-sm text-muted-foreground">Aucun joueur trouvé.</p>
                </div>
            </div>
        </div>

        <!-- Add player modal -->
        <Teleport to="body">
            <Transition name="fade">
                <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center">
                    <div class="absolute inset-0 bg-black/40" @click="closeAddModal" />

                    <div class="relative z-10 mx-4 w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                        <!-- Header -->
                        <div class="mb-6 flex items-center gap-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-violet-100">
                                <UserPlus class="h-7 w-7 text-violet-500" />
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-foreground">Nouveau joueur</h3>
                                <p class="text-sm text-muted-foreground">Ajouter un joueur au groupe</p>
                            </div>
                        </div>

                        <!-- Code input -->
                        <div class="mb-5">
                            <Label for="add-code" class="mb-2 block text-sm text-muted-foreground">Code à 6 chiffres</Label>
                            <Input
                                id="add-code"
                                v-model="addForm.code"
                                type="text"
                                inputmode="numeric"
                                maxlength="6"
                                placeholder="0 0 0 – 0 0 0"
                                class="h-12 rounded-xl bg-gray-50 text-lg tracking-widest shadow-none focus-visible:ring-1 focus-visible:ring-primary"
                                @input="formatCode"
                            />
                            <p v-if="addForm.errors.code" class="mt-1.5 text-sm text-destructive">{{ addForm.errors.code }}</p>
                        </div>

                        <!-- ELO input -->
                        <div class="mb-6">
                            <Label for="add-elo" class="mb-2 block text-sm text-muted-foreground">
                                ELO de départ <span class="text-muted-foreground/60">(par défaut 100.0)</span>
                            </Label>
                            <Input
                                id="add-elo"
                                v-model.number="addForm.elo"
                                type="number"
                                min="0"
                                class="h-12 rounded-xl bg-gray-50 text-lg font-semibold shadow-none focus-visible:ring-1 focus-visible:ring-primary"
                            />
                        </div>

                        <!-- Actions -->
                        <div class="grid grid-cols-2 gap-3">
                            <Button
                                variant="outline"
                                size="lg"
                                class="rounded-xl"
                                @click="closeAddModal"
                            >
                                Annuler
                            </Button>
                            <Button
                                size="lg"
                                class="rounded-xl"
                                :disabled="addForm.processing"
                                @click="confirmAdd"
                            >
                                <UserPlus class="h-4 w-4" />
                                Ajouter
                            </Button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Edit modal -->
        <Teleport to="body">
            <Transition name="fade">
                <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center">
                    <div class="absolute inset-0 bg-black/40" @click="closeEditModal" />

                    <div class="relative z-10 mx-4 w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                        <!-- Player header -->
                        <div v-if="playerToEdit" class="mb-6 flex items-center gap-4 border-b border-border pb-5">
                            <Avatar class="h-14 w-14 shrink-0">
                                <AvatarImage v-if="playerToEdit.avatar" :src="playerToEdit.avatar" />
                                <AvatarFallback class="text-sm">{{ getInitials(playerToEdit.name) }}</AvatarFallback>
                            </Avatar>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-lg font-bold text-foreground">{{ playerToEdit.name }}</h3>
                                    <span v-if="editForm.make_admin" class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-600">
                                        <Crown class="h-3 w-3" />
                                        Admin
                                    </span>
                                </div>
                                <p class="text-sm text-muted-foreground">Modifier le classement ELO</p>
                            </div>
                        </div>

                        <!-- Current ELO -->
                        <div class="mb-5 flex items-center justify-between rounded-xl bg-gray-50 px-4 py-3">
                            <span class="text-sm text-muted-foreground">ELO actuel</span>
                            <span class="text-lg font-bold text-foreground">{{ playerToEdit?.elo }}</span>
                        </div>

                        <!-- New ELO input -->
                        <div class="mb-5">
                            <Label for="edit-elo" class="mb-2 block text-sm font-medium text-muted-foreground">Nouveau ELO</Label>
                            <Input
                                id="edit-elo"
                                v-model.number="editForm.elo"
                                type="number"
                                min="0"
                                class="h-12 rounded-xl bg-gray-50 text-lg font-semibold shadow-none focus-visible:ring-1 focus-visible:ring-primary"
                            />
                        </div>

                        <!-- Divider -->
                        <div class="mb-5 border-t border-border" />

                        <!-- Toggle options -->
                        <div class="mb-6 grid grid-cols-2 gap-3">
                            <div class="flex items-center justify-between gap-5 rounded-2xl border border-border bg-gray-50/50 px-5 py-4">
                                <span class="whitespace-nowrap text-sm text-muted-foreground">Joueur actif</span>
                                <Switch
                                    v-model="editIsActive"
                                    class="h-7 w-12 shrink-0"
                                />
                            </div>
                            <button
                                type="button"
                                class="flex items-center justify-center gap-2 whitespace-nowrap rounded-2xl border px-4 py-3.5 text-sm font-medium transition-all cursor-pointer"
                                :class="editForm.make_admin
                                    ? 'bg-destructive/10 border-destructive/30 text-destructive hover:bg-destructive/15'
                                    : 'bg-gray-50/50 border-border text-muted-foreground hover:bg-amber-50 hover:border-amber-300 hover:text-amber-600'"
                                @click="editForm.make_admin = !editForm.make_admin"
                            >
                                <ShieldOff v-if="editForm.make_admin" class="h-4 w-4 shrink-0" />
                                <Crown v-else class="h-4 w-4 shrink-0" />
                                {{ editForm.make_admin ? 'Retirer admin' : 'Nommer admin' }}
                            </button>
                        </div>

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
                                Enregistrer
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
                        <div class="mb-4 flex justify-center">
                            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-50">
                                <AlertTriangle class="h-7 w-7 text-destructive" />
                            </div>
                        </div>

                        <h3 class="mb-4 text-center text-lg font-bold text-foreground">
                            Retirer ce joueur ?
                        </h3>

                        <div v-if="playerToDelete" class="mb-4 flex items-center justify-center gap-3 rounded-xl bg-gray-50 px-4 py-3">
                            <Avatar class="h-10 w-10 shrink-0">
                                <AvatarImage v-if="playerToDelete.avatar" :src="playerToDelete.avatar" />
                                <AvatarFallback class="text-xs">{{ getInitials(playerToDelete.name) }}</AvatarFallback>
                            </Avatar>
                            <span class="text-sm font-semibold text-foreground">{{ playerToDelete.name }}</span>
                        </div>

                        <p class="mb-6 text-center text-sm text-muted-foreground">
                            Le joueur sera retiré du cours et son historique ELO sera supprimé.
                        </p>

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
