<script setup>
import { ref, reactive, computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Input } from '@/Components/ui/input';
import { Button } from '@/Components/ui/button';
import {
    ArrowLeft,
    Shield,
    Search,
    Trash2,
    AlertTriangle,
    Gamepad2,
    Mail,
} from 'lucide-vue-next';

const props = defineProps({
    admins: { type: Array, default: () => [] },
    adminCount: { type: Number, default: 0 },
});

const currentUserId = usePage().props.auth.user?.id;
const search = ref('');
const expandedClasses = reactive(new Set());

const filteredAdmins = computed(() => {
    if (!search.value.trim()) return props.admins;
    const q = search.value.toLowerCase();
    return props.admins.filter(a =>
        a.name.toLowerCase().includes(q) || a.email.toLowerCase().includes(q)
    );
});

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(p => p.charAt(0)).join('').toUpperCase();
};

// Delete modal
const showDeleteModal = ref(false);
const adminToDelete = ref(null);
const deleteForm = useForm({});

const openDeleteModal = (admin) => {
    adminToDelete.value = admin;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    adminToDelete.value = null;
};

const confirmDelete = () => {
    if (!adminToDelete.value) return;
    deleteForm.delete(route('admin.admins.destroy', adminToDelete.value.id), {
        onSuccess: () => closeDeleteModal(),
        onError: () => toast.error("Erreur lors de la suppression de l'administrateur."),
    });
};
</script>

<template>
    <Head title="Administrateurs - Admin" />

    <AdminLayout>
        <div class="p-8">
            <!-- Back link -->
            <Link :href="route('admin.dashboard')" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground transition-colors mb-6">
                <ArrowLeft class="h-4 w-4" />
                Retour au dashboard
            </Link>

            <!-- Header card -->
            <div class="rounded-2xl border border-border p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100">
                            <Shield class="h-6 w-6 text-blue-500" />
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="text-xl font-bold text-foreground">Administrateurs</h1>
                                <span class="inline-flex h-6 min-w-6 items-center justify-center rounded-full bg-gray-100 px-2 text-xs font-semibold text-foreground">{{ adminCount }}</span>
                            </div>
                            <p class="text-sm text-muted-foreground">Gérer les administrateurs de vos cours</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div class="flex items-center gap-4 mb-4">
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                    <Input
                        v-model="search"
                        type="text"
                        placeholder="Rechercher un administrateur..."
                        class="pl-9 w-full text-sm placeholder:text-gray-400 shadow-none focus-visible:ring-1 focus-visible:ring-primary"
                    />
                </div>
            </div>

            <!-- Admin list -->
            <div class="rounded-2xl border border-border divide-y">
                <div
                    v-for="admin in filteredAdmins"
                    :key="admin.id"
                    class="flex items-center gap-4 px-5 py-4"
                >
                    <!-- Avatar -->
                    <Avatar class="h-11 w-11 shrink-0">
                        <AvatarImage v-if="admin.avatar" :src="admin.avatar" />
                        <AvatarFallback class="text-xs">{{ getInitials(admin.name) }}</AvatarFallback>
                    </Avatar>

                    <!-- Info -->
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-foreground">{{ admin.name }}</span>
                            <span v-if="admin.isPlayer" class="inline-flex items-center gap-1 rounded-full bg-violet-100 px-1.5 py-0.5 text-xs font-semibold text-violet-600">
                                <Gamepad2 class="h-3 w-3" />
                                Joueur
                            </span>
                        </div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <Mail class="h-3 w-3 text-muted-foreground" />
                            <span class="text-xs text-muted-foreground">{{ admin.email }}</span>
                        </div>
                    </div>

                    <!-- Classes -->
                    <div class="flex flex-wrap gap-1.5 shrink-0">
                        <span
                            v-for="cls in admin.classes.slice(0, expandedClasses.has(admin.id) ? undefined : 3)"
                            :key="cls.id"
                            class="inline-flex items-center rounded-full border border-border bg-gray-50 px-2.5 py-0.5 text-xs text-muted-foreground"
                        >
                            {{ cls.name }}
                        </span>
                        <button
                            v-if="admin.classes.length > 3 && !expandedClasses.has(admin.id)"
                            class="inline-flex items-center rounded-full border border-border bg-gray-50 px-2.5 py-0.5 text-xs text-muted-foreground hover:bg-gray-100 cursor-pointer transition-colors"
                            @click="expandedClasses.add(admin.id)"
                        >
                            +{{ admin.classes.length - 3 }}
                        </button>
                    </div>

                    <!-- Date -->
                    <div class="text-right shrink-0">
                        <p class="text-xs text-muted-foreground">Depuis le</p>
                        <p class="text-sm font-medium text-foreground">{{ admin.createdAt }}</p>
                    </div>

                    <!-- Actions -->
                    <div v-if="admin.userId !== currentUserId" class="flex items-center gap-2 shrink-0">
                        <button
                            class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-lg transition-colors hover:bg-red-50"
                            @click="openDeleteModal(admin)"
                        >
                            <Trash2 class="h-4 w-4 text-destructive" />
                        </button>
                    </div>
                </div>

                <!-- Empty state -->
                <div v-if="filteredAdmins.length === 0" class="py-12 text-center">
                    <p class="text-sm text-muted-foreground">Aucun administrateur trouvé.</p>
                </div>
            </div>
        </div>

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
                            Retirer cet administrateur ?
                        </h3>

                        <div v-if="adminToDelete" class="mb-4 flex items-center justify-center gap-3 rounded-xl bg-gray-50 px-4 py-3">
                            <Avatar class="h-10 w-10 shrink-0">
                                <AvatarImage v-if="adminToDelete.avatar" :src="adminToDelete.avatar" />
                                <AvatarFallback class="text-xs">{{ getInitials(adminToDelete.name) }}</AvatarFallback>
                            </Avatar>
                            <span class="text-sm font-semibold text-foreground">{{ adminToDelete.name }}</span>
                        </div>

                        <p class="mb-6 text-center text-sm text-muted-foreground">
                            Cette personne perdra ses droits d'administration sur tous vos cours.
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
                                Retirer
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
