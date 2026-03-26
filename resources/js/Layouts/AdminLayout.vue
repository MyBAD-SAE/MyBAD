<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Button } from '@/Components/ui/button';
import { LayoutDashboard, Swords, Trophy, Plus, ChevronDown, Smartphone } from 'lucide-vue-next';

const page = usePage();
const user = page.props.auth.user;

const adminUser = page.props.adminUser ?? {
    fullName: user?.first_name ?? 'Admin',
    avatar: user?.profile_picture,
};

const navItems = [
    { label: 'Dashboard', icon: LayoutDashboard, routeName: 'admin.dashboard' },
    { label: 'Matchs', icon: Swords, routeName: 'admin.matchs' },
    { label: 'Regles et defis', icon: Trophy, routeName: 'admin.regles' },
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
        <aside class="flex w-[280px] shrink-0 flex-col border-r bg-white">
            <!-- Logo -->
            <div class="px-6 pt-6 pb-4">
                <span class="text-xl font-bold text-foreground">MyBAD</span>
            </div>

            <!-- Nouvelle seance button -->
            <div class="px-4 pb-6">
                <Button class="w-full justify-start gap-2 bg-primary text-white hover:bg-primary/90">
                    <Plus class="h-4 w-4" />
                    Nouvelle seance
                </Button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 space-y-1 px-3">
                <Link
                    v-for="item in navItems"
                    :key="item.routeName"
                    :href="route(item.routeName)"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors"
                    :class="isActive(item.routeName) ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-gray-100 hover:text-foreground'"
                >
                    <component :is="item.icon" class="h-5 w-5" />
                    {{ item.label }}
                </Link>
            </nav>

            <!-- Admin profile -->
            <div class="border-t p-4">
                <div class="flex items-center gap-3">
                    <Avatar class="h-10 w-10">
                        <AvatarImage v-if="adminUser.avatar" :src="adminUser.avatar" />
                        <AvatarFallback class="text-xs">{{ getInitials(adminUser.fullName) }}</AvatarFallback>
                    </Avatar>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-foreground">{{ adminUser.fullName }}</p>
                        <p class="text-xs text-muted-foreground">Administrateur</p>
                    </div>
                    <ChevronDown class="h-4 w-4 shrink-0 text-muted-foreground" />
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <main class="flex-1 overflow-y-auto">
            <slot />
        </main>
    </div>
</template>

