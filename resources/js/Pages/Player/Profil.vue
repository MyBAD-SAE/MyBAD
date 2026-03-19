<script setup>
import { Head, usePage, useForm } from '@inertiajs/vue3';
import PlayerLayout from '@/Layouts/PlayerLayout.vue';
import BottomNavBar from '@/Components/BottomNavBar.vue';
import { Avatar, AvatarImage, AvatarFallback } from '@/Components/ui/avatar';
import { Card, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Separator } from '@/Components/ui/separator';
import {
    Camera,
    Pencil,
    TrendingUp,
    Swords,
    Trophy,
    Crown,
    UserRound,
    Shield,
    ChartNoAxesCombined,
    CalendarDays,
    ChevronRight,
    LogOut,
} from 'lucide-vue-next';

const user = usePage().props.auth.user;

const logoutForm = useForm({});

const stats = [
    { icon: TrendingUp, value: '124.7', label: 'ELO', color: 'text-emerald-500', bg: 'bg-emerald-50' },
    { icon: Swords, value: '22', label: 'Matchs', color: 'text-violet-500', bg: 'bg-violet-50' },
    { icon: Trophy, value: '18', label: 'Victoires', color: 'text-teal-500', bg: 'bg-teal-50' },
    { icon: Crown, value: '82%', label: 'Win Rate', color: 'text-amber-500', bg: 'bg-amber-50' },
];

const menuItems = [
    { icon: UserRound, title: 'Informations personnelles', subtitle: 'Nom, email, mot de passe', color: 'text-blue-500', bg: 'bg-blue-50' },
    { icon: Shield, title: 'Confidentialité', subtitle: 'Données personnelles', color: 'text-violet-500', bg: 'bg-violet-50' },
    { icon: ChartNoAxesCombined, title: 'Historique & statistiques', subtitle: '22 matchs joués', color: 'text-rose-500', bg: 'bg-rose-50' },
    { icon: CalendarDays, title: 'Séance du jour', subtitle: 'Choisir un jour', color: 'text-pink-500', bg: 'bg-pink-50' },
];

function logout() {
    logoutForm.post(route('player.logout'));
}
</script>

<template>
    <Head title="Profil" />

    <PlayerLayout>
        <div class="pb-20">
            <!-- Banner gradient -->
            <div class="relative mb-16">
                <div class="h-32 bg-linear-to-r from-pink-400 via-orange-400 to-purple-500" />

                <!-- Avatar + Nom côte à côte -->
                <div class="absolute bottom-0 left-0 z-10 flex items-end gap-4 px-4 translate-y-1/2">
                    <div class="relative shrink-0">
                        <Avatar class="h-24 w-24 border-4 border-background shadow-lg">
                            <AvatarImage v-if="user.profile_picture" :src="user.profile_picture" :alt="user.full_name" />
                            <AvatarFallback class="text-2xl">{{ user.first_name?.charAt(0) }}</AvatarFallback>
                        </Avatar>
                        <button class="absolute bottom-0 right-0 flex h-8 w-8 items-center justify-center rounded-full bg-primary text-white shadow-md">
                            <Camera class="h-4 w-4" />
                        </button>
                    </div>
                    <div class="pb-1">
                        <h1 class="text-xl font-bold text-foreground">{{ user.first_name }} {{ user.last_name }}</h1>
                        <div class="mt-0.5 flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-600">
                                <Crown class="h-3 w-3" /> #1
                            </span>
                            <span class="text-sm text-muted-foreground">ID : {{ user.id?.slice(0, 6) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bouton modifier -->
            <div class="px-4 pb-4">
                <button class="flex w-full items-center justify-center gap-2 rounded-2xl border border-border bg-muted/50 py-3.5 text-sm font-medium text-foreground">
                    <Pencil class="h-4 w-4 text-muted-foreground" />
                    Modifier le profil
                </button>
            </div>

            <!-- Stats -->
            <div class="mx-4 mt-4 grid grid-cols-4 gap-2">
                <Card v-for="stat in stats" :key="stat.label" class="py-3">
                    <CardContent class="flex flex-col items-center gap-1 px-2">
                        <div :class="[stat.bg, 'flex h-10 w-10 items-center justify-center rounded-full']">
                            <component :is="stat.icon" class="h-5 w-5" :class="stat.color" />
                        </div>
                        <span class="text-lg font-semibold text-foreground">{{ stat.value }}</span>
                        <span class="text-[10px] text-muted-foreground">{{ stat.label }}</span>
                    </CardContent>
                </Card>
            </div>

            <!-- Menu Compte -->
            <Card class="mx-4 mt-4">
                <CardContent class="px-4 py-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Compte</span>

                    <div class="mt-3 space-y-0">
                        <template v-for="(item, index) in menuItems" :key="item.title">
                            <button class="flex w-full items-center gap-3 py-3">
                                <div :class="[item.bg, 'flex h-10 w-10 shrink-0 items-center justify-center rounded-full']">
                                    <component :is="item.icon" class="h-5 w-5" :class="item.color" />
                                </div>
                                <div class="flex-1 text-left">
                                    <p class="text-sm font-medium text-foreground">{{ item.title }}</p>
                                    <p class="text-xs text-muted-foreground">{{ item.subtitle }}</p>
                                </div>
                                <ChevronRight class="h-4 w-4 text-muted-foreground" />
                            </button>
                            <Separator v-if="index < menuItems.length - 1" />
                        </template>
                    </div>

                    <!-- Déconnexion -->
                    <Button
                        variant="outline"
                        class="mt-4 w-full border-red-200 bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600"
                        size="lg"
                        @click="logout"
                    >
                        <LogOut class="h-4 w-4" />
                        Se déconnecter
                    </Button>

                    <!-- Version -->
                    <p class="mt-3 text-center text-xs text-muted-foreground">MyBAD v1.2.0 · Mars 2026</p>
                </CardContent>
            </Card>
        </div>

        <BottomNavBar active="profil" />
    </PlayerLayout>
</template>
