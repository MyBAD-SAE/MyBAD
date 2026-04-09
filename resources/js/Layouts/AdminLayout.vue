<script setup>
import { ref } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { Toaster } from '@/Components/ui/sonner';
import { useFlashToast } from '@/Composables/useFlashToast';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Button } from '@/Components/ui/button';
import {
    AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent,
    AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle,
} from '@/Components/ui/alert-dialog';
import { LayoutDashboard, Users, Shield, Swords, Trophy, Plus, LogOut, Smartphone } from 'lucide-vue-next';
useFlashToast();

const showLogoutDialog = ref(false);

const startSession = () => {
    router.post(route('admin.session.store'));
};

const logout = () => {
    router.post(route('admin.logout'));
};

const page = usePage();
const user = page.props.auth.user;

const adminUser = page.props.adminUser ?? {
    fullName: user?.first_name ?? 'Admin',
    avatar: user?.profile_picture,
};

const isPureAdmin = page.props.adminUser?.isPureAdmin ?? false;

const navItems = [
    { label: 'Dashboard', icon: LayoutDashboard, routeName: 'admin.dashboard' },
    { label: 'Joueurs', icon: Users, routeName: 'admin.players' },
    { label: 'Matchs', icon: Swords, routeName: 'admin.matches' },
    { label: 'Regles et defis', icon: Trophy, routeName: 'admin.rules' },
    ...(isPureAdmin ? [{ label: 'Admins', icon: Shield, routeName: 'admin.admins' }] : []),
];

const isActive = (routeName) => route().current(routeName);

const getInitials = (name) => {
    if (!name) return 'A';
    return name.split(' ').map(p => p.charAt(0)).join('').toUpperCase();
};
</script>

<template>
    <!-- Mobile / Tablette : message de blocage -->
    <div class="flex min-h-screen lg:hidden flex-col items-center justify-center bg-background px-8 text-center">
        <img src="/images/logo_mybad.png" alt="MyBAD" class="h-16 w-16 rounded-xl" />
        <h1 class="mt-6 text-xl font-bold text-foreground">Interface desktop uniquement</h1>
        <p class="mt-2 max-w-sm text-sm text-muted-foreground">
            L'espace administrateur de MyBAD est conçu pour les ordinateurs. Veuillez vous connecter depuis un ordinateur pour accéder à l'application.
        </p>
        <div class="mt-6 flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-3">
            <Smartphone class="h-5 w-5 text-muted-foreground" />
            <span class="text-sm text-muted-foreground">Mobile et tablette non supportés</span>
        </div>
    </div>

    <!-- Desktop : contenu normal -->
    <div class="hidden lg:flex h-screen bg-gray-50">
        <!-- Sidebar -->
        <aside class="flex w-[280px] shrink-0 flex-col bg-gray-50">
            <!-- Logo -->
            <div class="flex items-center gap-2.5 px-6 pt-6 pb-4">
                <img src="/images/logo_mybad.png" alt="MyBAD" class="h-8 w-8 rounded-md" />
                <span class="text-lg font-bold text-foreground">MyBAD</span>
            </div>

            <!-- Nouvelle seance button -->
            <div class="px-4 pt-4 pb-4">
                <button
                    @click="startSession"
                    class="flex w-full cursor-pointer items-center gap-3 rounded-2xl bg-primary px-5 py-3 text-base font-medium text-white transition-colors hover:bg-primary/90"
                >
                    <Plus class="h-5 w-5" />
                    Nouvelle séance
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 space-y-1 px-4">
                <Link
                    v-for="item in navItems"
                    :key="item.routeName"
                    :href="route(item.routeName)"
                    class="flex cursor-pointer items-center gap-3 rounded-lg px-4 py-3 text-sm font-normal transition-colors"
                    :class="isActive(item.routeName) ? 'text-primary' : 'text-muted-foreground hover:text-foreground'"
                >
                    <component :is="item.icon" class="h-5 w-5" />
                    {{ item.label }}
                </Link>
            </nav>

            <!-- Admin profile -->
            <div class="flex items-center gap-2 p-4">
                <Link :href="route('admin.account')" class="flex min-w-0 flex-1 cursor-pointer items-center gap-3 rounded-lg px-1 py-1 transition-colors hover:bg-gray-100">
                    <Avatar class="h-10 w-10">
                        <AvatarImage v-if="adminUser.avatar" :src="adminUser.avatar" />
                        <AvatarFallback class="text-xs">{{ getInitials(adminUser.fullName) }}</AvatarFallback>
                    </Avatar>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-foreground">{{ adminUser.fullName }}</p>
                        <p class="text-xs text-muted-foreground">Administrateur</p>
                    </div>
                </Link>
                <button @click="showLogoutDialog = true" class="shrink-0 cursor-pointer rounded-lg p-2 text-muted-foreground transition-colors hover:bg-gray-100 hover:text-red-500">
                    <LogOut class="h-4 w-4" />
                </button>
            </div>

            <!-- Logout confirmation dialog -->
            <AlertDialog :open="showLogoutDialog" @update:open="showLogoutDialog = $event">
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
        </aside>

        <!-- Main content -->
        <main class="flex-1 overflow-y-auto p-6">
            <div class="rounded-2xl border border-gray-200 bg-white min-h-full">
                <slot />
            </div>
        </main>
    </div>

    <Toaster position="bottom-right" :duration="4000" theme="light" />
</template>

