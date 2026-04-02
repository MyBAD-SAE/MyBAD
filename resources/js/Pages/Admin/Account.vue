<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import {
    AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent,
    AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle,
} from '@/Components/ui/alert-dialog';
import {
    ArrowLeft,
    UserRound,
    Mail,
    Lock,
    Eye,
    EyeOff,
    Save,
    LogOut,
    Shield,
} from 'lucide-vue-next';

const props = defineProps({
    user: { type: Object, required: true },
    adminUser: { type: Object, default: () => ({}) },
});

const profileForm = useForm({
    first_name: props.user.first_name,
    last_name: props.user.last_name,
    email: props.user.email,
});

const passwordForm = useForm({
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
});

const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

const passwordMismatch = computed(() => {
    return passwordForm.new_password_confirmation.length > 0
        && passwordForm.new_password.length > 0
        && passwordForm.new_password_confirmation !== passwordForm.new_password;
});

function saveProfile() {
    profileForm.put(route('admin.account.profile'), {
        preserveScroll: true,
        onError: () => toast.error('Erreur lors de la mise à jour du profil.'),
    });
}

function changePassword() {
    if (passwordMismatch.value) return;
    passwordForm.put(route('admin.account.password'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
        },
        onError: () => toast.error('Erreur lors du changement de mot de passe.'),
    });
}

const getInitials = (name) => {
    if (!name) return 'A';
    return name.split(' ').map(p => p.charAt(0)).join('').toUpperCase();
};

const showLogoutModal = ref(false);
const logoutForm = useForm({});

function logout() {
    logoutForm.post(route('admin.logout'));
}
</script>

<template>
    <Head title="Mon compte" />

    <AdminLayout>
        <div class="p-8">
            <!-- Header -->
            <div class="flex items-start gap-4 mb-4">
                <Link :href="route('admin.dashboard')" class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-border/50 transition-colors hover:bg-gray-50">
                    <ArrowLeft class="h-5 w-5 text-foreground" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-foreground">Mon compte</h1>
                    <p class="text-sm text-muted-foreground">Gérer vos informations administrateur</p>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Profil card -->
                <div class="rounded-2xl border border-border p-6">
                    <div class="flex items-center gap-4">
                        <Avatar class="h-16 w-16 bg-gray-100">
                            <AvatarImage v-if="user.profile_picture" :src="user.profile_picture" />
                            <AvatarFallback class="bg-gray-100">
                                <Shield class="h-7 w-7 text-muted-foreground" />
                            </AvatarFallback>
                        </Avatar>
                        <div>
                            <p class="text-lg font-semibold text-foreground">{{ user.first_name }} {{ user.last_name }}</p>
                            <div class="flex items-center gap-1.5 mt-1">
                                <span class="h-2 w-2 rounded-full bg-primary" />
                                <span class="text-sm text-muted-foreground">Administrateur</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2 colonnes : infos perso + mot de passe -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Informations personnelles -->
                    <div class="rounded-2xl border border-border p-5 space-y-5">
                        <h2 class="text-base font-semibold text-foreground">Informations personnelles</h2>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-muted-foreground">Prénom</label>
                                <div class="mt-1.5 flex items-center gap-2.5 rounded-lg border border-border bg-gray-50/50 px-3 py-1.5 transition-colors focus-within:border-primary focus-within:ring-[3px] focus-within:ring-primary/20">
                                    <UserRound class="h-4 w-4 shrink-0 text-muted-foreground" />
                                    <input v-model="profileForm.first_name" type="text" class="flex-1 border-none bg-transparent text-sm text-foreground shadow-none outline-none ring-0 focus:ring-0" />
                                </div>
                                <p v-if="profileForm.errors.first_name" class="mt-1.5 text-xs text-destructive">{{ profileForm.errors.first_name }}</p>
                            </div>

                            <div>
                                <label class="text-xs text-muted-foreground">Nom</label>
                                <div class="mt-1.5 flex items-center gap-2.5 rounded-lg border border-border bg-gray-50/50 px-3 py-1.5 transition-colors focus-within:border-primary focus-within:ring-[3px] focus-within:ring-primary/20">
                                    <UserRound class="h-4 w-4 shrink-0 text-muted-foreground" />
                                    <input v-model="profileForm.last_name" type="text" class="flex-1 border-none bg-transparent text-sm text-foreground shadow-none outline-none ring-0 focus:ring-0" />
                                </div>
                                <p v-if="profileForm.errors.last_name" class="mt-1.5 text-xs text-destructive">{{ profileForm.errors.last_name }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="text-xs text-muted-foreground">Email</label>
                            <div class="mt-1.5 flex items-center gap-2.5 rounded-lg border border-border bg-gray-50/50 px-3 py-1.5 transition-colors focus-within:border-primary focus-within:ring-[3px] focus-within:ring-primary/20">
                                <Mail class="h-4 w-4 shrink-0 text-muted-foreground" />
                                <input v-model="profileForm.email" type="email" class="flex-1 border-none bg-transparent text-sm text-foreground shadow-none outline-none ring-0 focus:ring-0" />
                            </div>
                            <p v-if="profileForm.errors.email" class="mt-1.5 text-xs text-destructive">{{ profileForm.errors.email }}</p>
                        </div>

                        <button
                            class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl bg-primary py-3 text-sm font-semibold text-white transition-colors hover:bg-primary/90"
                            :disabled="profileForm.processing"
                            @click="saveProfile"
                        >
                            <Save class="h-4 w-4" />
                            Enregistrer
                        </button>
                    </div>

                    <!-- Modifier le mot de passe -->
                    <div class="rounded-2xl border border-border p-5 space-y-5">
                        <h2 class="text-base font-semibold text-foreground">Modifier le mot de passe</h2>

                        <div>
                            <label class="text-xs text-muted-foreground">Mot de passe actuel</label>
                            <div
                                :class="[
                                    'mt-1.5 flex items-center gap-2.5 rounded-lg px-3 py-1.5 transition-colors focus-within:border-primary focus-within:ring-[3px] focus-within:ring-primary/20',
                                    passwordForm.errors.current_password ? 'border border-red-400 bg-red-50' : 'border border-border bg-gray-50/50',
                                ]"
                            >
                                <Lock class="h-4 w-4 shrink-0 text-muted-foreground" />
                                <input
                                    v-model="passwordForm.current_password"
                                    :type="showCurrentPassword ? 'text' : 'password'"
                                    placeholder="••••••••"
                                    class="min-w-0 flex-1 border-none bg-transparent text-sm text-foreground shadow-none outline-none ring-0 placeholder:text-muted-foreground/50 focus:ring-0"
                                />
                                <button type="button" class="shrink-0" @click="showCurrentPassword = !showCurrentPassword">
                                    <component :is="showCurrentPassword ? EyeOff : Eye" class="h-4 w-4 text-muted-foreground/50" />
                                </button>
                            </div>
                            <p v-if="passwordForm.errors.current_password" class="mt-1.5 text-xs text-destructive">{{ passwordForm.errors.current_password }}</p>
                        </div>

                        <div>
                            <label class="text-xs text-muted-foreground">Nouveau mot de passe</label>
                            <div
                                :class="[
                                    'mt-1.5 flex items-center gap-2.5 rounded-lg px-3 py-1.5 transition-colors focus-within:border-primary focus-within:ring-[3px] focus-within:ring-primary/20',
                                    passwordForm.errors.new_password ? 'border border-red-400 bg-red-50' : 'border border-border bg-gray-50/50',
                                ]"
                            >
                                <Lock class="h-4 w-4 shrink-0 text-muted-foreground" />
                                <input
                                    v-model="passwordForm.new_password"
                                    :type="showNewPassword ? 'text' : 'password'"
                                    placeholder="••••••••"
                                    class="min-w-0 flex-1 border-none bg-transparent text-sm text-foreground shadow-none outline-none ring-0 placeholder:text-muted-foreground/50 focus:ring-0"
                                />
                                <button type="button" class="shrink-0" @click="showNewPassword = !showNewPassword">
                                    <component :is="showNewPassword ? EyeOff : Eye" class="h-4 w-4 text-muted-foreground/50" />
                                </button>
                            </div>
                            <p v-if="passwordForm.errors.new_password" class="mt-1.5 text-xs text-destructive">{{ passwordForm.errors.new_password }}</p>
                        </div>

                        <div>
                            <label class="text-xs text-muted-foreground">Confirmer le mot de passe</label>
                            <div
                                :class="[
                                    'mt-1.5 flex items-center gap-2.5 rounded-lg px-3 py-1.5 transition-colors focus-within:border-primary focus-within:ring-[3px] focus-within:ring-primary/20',
                                    passwordMismatch ? 'border border-red-400 bg-red-50' : 'border border-border bg-gray-50/50',
                                ]"
                            >
                                <Lock class="h-4 w-4 shrink-0 text-muted-foreground" />
                                <input
                                    v-model="passwordForm.new_password_confirmation"
                                    :type="showConfirmPassword ? 'text' : 'password'"
                                    placeholder="••••••••"
                                    class="min-w-0 flex-1 border-none bg-transparent text-sm text-foreground shadow-none outline-none ring-0 placeholder:text-muted-foreground/50 focus:ring-0"
                                />
                                <button type="button" class="shrink-0" @click="showConfirmPassword = !showConfirmPassword">
                                    <component :is="showConfirmPassword ? EyeOff : Eye" class="h-4 w-4 text-muted-foreground/50" />
                                </button>
                            </div>
                            <p v-if="passwordMismatch" class="mt-1.5 text-xs text-red-500">Les mots de passe ne correspondent pas</p>
                        </div>

                        <button
                            class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl bg-gray-900 py-3 text-sm font-semibold text-white transition-colors hover:bg-gray-800"
                            :disabled="passwordForm.processing || passwordMismatch"
                            @click="changePassword"
                        >
                            <Lock class="h-4 w-4" />
                            Changer le mot de passe
                        </button>
                    </div>
                </div>

                <!-- Se déconnecter -->
                <button
                    class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-2xl border border-border py-3 text-sm font-medium text-destructive transition-colors hover:bg-red-50"
                    @click="showLogoutModal = true"
                >
                    <LogOut class="h-4 w-4" />
                    Se déconnecter
                </button>
            </div>
        </div>

        <!-- Modal déconnexion -->
        <AlertDialog :open="showLogoutModal" @update:open="showLogoutModal = $event">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Se déconnecter ?</AlertDialogTitle>
                    <AlertDialogDescription>
                        Vous allez être déconnecté de votre espace administrateur.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Annuler</AlertDialogCancel>
                    <AlertDialogAction @click="logout">Se déconnecter</AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>

    </AdminLayout>
</template>
