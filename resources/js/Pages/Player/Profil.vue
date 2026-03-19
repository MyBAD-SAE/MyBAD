<script setup>
import { ref } from 'vue';
import { Head, usePage, useForm, Link } from '@inertiajs/vue3';
import PlayerLayout from '@/Layouts/PlayerLayout.vue';
import BottomNavBar from '@/Components/BottomNavBar.vue';
import SessionPicker from '@/Components/dashboard/SessionPicker.vue';
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
const selectedDay = ref('Mardi');

const stats = [
    { icon: TrendingUp, value: '124.7', label: 'ELO', color: 'text-emerald-500', bg: 'bg-emerald-50' },
    { icon: Swords, value: '22', label: 'Matchs', color: 'text-violet-500', bg: 'bg-violet-50' },
    { icon: Trophy, value: '18', label: 'Victoires', color: 'text-teal-500', bg: 'bg-teal-50' },
    { icon: Crown, value: '82%', label: 'Win Rate', color: 'text-amber-500', bg: 'bg-amber-50' },
];

const menuItems = [
    { icon: UserRound, title: 'Informations personnelles', subtitle: 'Nom, email, mot de passe', color: 'text-blue-500', bg: 'bg-blue-50', routeName: 'profil.infos' },
    { icon: Shield, title: 'Confidentialité', subtitle: 'Données personnelles', color: 'text-violet-500', bg: 'bg-violet-50', routeName: null },
    { icon: ChartNoAxesCombined, title: 'Historique & statistiques', subtitle: '22 matchs joués', color: 'text-rose-500', bg: 'bg-rose-50', routeName: null },
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
            <div class="relative">
                <div class="h-36 bg-linear-to-r from-pink-400 via-orange-400 to-purple-500" />

                <!-- Avatar en absolute, chevauche la bannière -->
                <div class="absolute bottom-0 left-4 z-10 translate-y-1/2">
                    <div class="relative">
                        <Avatar class="h-24 w-24 border-4 border-background shadow-lg">
                            <AvatarImage v-if="user.profile_picture" :src="user.profile_picture" :alt="user.full_name" />
                            <AvatarFallback class="text-2xl">{{ user.first_name?.charAt(0) }}</AvatarFallback>
                        </Avatar>
                        <button class="absolute bottom-0 right-0 flex h-8 w-8 items-center justify-center rounded-full bg-primary text-white shadow-md">
                            <Camera class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Nom + rang + ID — sous la bannière, décalé à droite de l'avatar -->
            <div class="pl-32 pr-4 pt-3 pb-4">
                <h1 class="text-xl font-bold text-foreground">{{ user.first_name }} {{ user.last_name }}</h1>
                <div class="mt-0.5 flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-600">
                        <Crown class="h-3 w-3" /> #1
                    </span>
                    <span class="text-sm text-muted-foreground">ID : {{ user.player?.code }}</span>
                </div>
            </div>

            <!-- Bouton modifier -->
            <div class="px-4 pb-4">
                <Link :href="route('profil.infos')" class="flex w-full items-center justify-center gap-2 rounded-2xl border border-border bg-muted/50 py-3.5 text-sm font-medium text-foreground">
                    <Pencil class="h-4 w-4 text-muted-foreground" />
                    Modifier le profil
                </Link>
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
            <Card class="mx-4 mt-4 gap-0 py-3">
                <CardContent class="px-4 py-0">
                    <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Compte</span>

                    <div class="mt-3 space-y-0">
                        <template v-for="(item, index) in menuItems" :key="item.title">
                            <component
                                :is="item.routeName ? Link : 'button'"
                                :href="item.routeName ? route(item.routeName) : undefined"
                                class="flex w-full items-center gap-3 py-3"
                            >
                                <div :class="[item.bg, 'flex h-10 w-10 shrink-0 items-center justify-center rounded-full']">
                                    <component :is="item.icon" class="h-5 w-5" :class="item.color" />
                                </div>
                                <div class="flex-1 text-left">
                                    <p class="text-sm font-medium text-foreground">{{ item.title }}</p>
                                    <p class="text-xs text-muted-foreground/60">{{ item.subtitle }}</p>
                                </div>
                                <ChevronRight class="h-4 w-4 text-muted-foreground" />
                            </component>
                            <Separator v-if="index < menuItems.length - 1" />
                        </template>

                        <!-- Séance du jour — ouvre le SessionPicker -->
                        <Separator />
                        <SessionPicker v-model="selectedDay">
                            <template #trigger>
                                <button class="flex w-full items-center gap-3 py-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-pink-50">
                                        <CalendarDays class="h-5 w-5 text-pink-500" />
                                    </div>
                                    <div class="flex-1 text-left">
                                        <p class="text-sm font-medium text-foreground">Séance du jour</p>
                                        <p class="text-xs text-muted-foreground/60">{{ selectedDay }}</p>
                                    </div>
                                    <ChevronRight class="h-4 w-4 text-muted-foreground" />
                                </button>
                            </template>
                        </SessionPicker>
                    </div>

                    <!-- Déconnexion -->
                    <button
                        class="mt-4 flex w-full items-center justify-center gap-2 rounded-2xl bg-red-100 py-3.5 text-sm font-medium text-red-500"
                        @click="logout"
                    >
                        <LogOut class="h-4 w-4" />
                        Se déconnecter
                    </button>

                    <!-- Version -->
                    <p class="mt-3 text-center text-xs text-muted-foreground">MyBAD v1.2.0 · Mars 2026</p>
                </CardContent>
            </Card>
        </div>

        <BottomNavBar active="profil" />
    </PlayerLayout>
</template>
