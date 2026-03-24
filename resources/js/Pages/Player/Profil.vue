<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import PlayerLayout from '@/Layouts/PlayerLayout.vue';
import BottomNavBar from '@/Components/BottomNavBar.vue';
import SessionPicker from '@/Components/dashboard/SessionPicker.vue';
import { Avatar, AvatarImage, AvatarFallback } from '@/Components/ui/avatar';
import { Card, CardContent } from '@/Components/ui/card';
import { Separator } from '@/Components/ui/separator';
import {
    Camera,
    Pencil,
    Crown,
    UserRound,
    Shield,
    ChartNoAxesCombined,
    CalendarDays,
    ChevronRight,
    LogOut,
} from 'lucide-vue-next';

const props = defineProps({
    participant: Object,
    user: Object,
    playerCode: String,
})

const logoutForm = useForm({});
const showLogoutModal = ref(false);
const selectedDay = ref('Mardi');

const player = computed(() => props.participant?.participantable);
const userInfo = computed(() => player.value?.user ?? props.user);

const menuItems = [
    { icon: UserRound, title: 'Informations personnelles', subtitle: 'Nom, email, mot de passe', color: 'text-blue-500', bg: 'bg-blue-50', routeName: 'player.account.infos' },
    { icon: Shield, title: 'Confidentialité', subtitle: 'Données personnelles', color: 'text-violet-500', bg: 'bg-violet-50', routeName: 'player.account.confidentialite' },
    { icon: ChartNoAxesCombined, title: 'Historique & statistiques', subtitle: '22 matchs joués', color: 'text-rose-500', bg: 'bg-rose-50', routeName: null },
];

const photoInput = ref(null);
const photoPreview = ref(null);

function selectPhoto() {
    photoInput.value.click();
}

function uploadPhoto(event) {
    const file = event.target.files[0];
    if (!file) return;

    photoPreview.value = URL.createObjectURL(file);

    router.post(route('player.account.photo.update'), { photo: file }, {
        forceFormData: true,
        preserveScroll: true,
        onFinish: () => {
            photoInput.value.value = '';
        },
    });
}

function logout() {
    logoutForm.post(route('player.account.logout'));
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
                            <AvatarImage v-if="photoPreview || userInfo?.profile_picture" :src="photoPreview || userInfo.profile_picture" :alt="userInfo?.first_name" />
                            <AvatarFallback class="text-2xl">{{ userInfo?.first_name?.charAt(0) }}</AvatarFallback>
                        </Avatar>
                        <button class="absolute bottom-0 right-0 flex h-8 w-8 items-center justify-center rounded-full bg-primary text-white shadow-md" @click="selectPhoto">
                            <Camera class="h-4 w-4" />
                        </button>
                        <input ref="photoInput" type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="uploadPhoto" />
                    </div>
                </div>
            </div>

            <!-- Nom + rang + ID — sous la bannière, décalé à droite de l'avatar -->
            <div class="pl-32 pr-4 pt-3 pb-4">
                <h1 class="text-xl font-bold text-foreground">{{ userInfo?.first_name }} {{ userInfo?.last_name }}</h1>
                <div class="mt-0.5 flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-600">
                        <Crown class="h-3 w-3" /> #{{ participant?.rank }}
                    </span>
                    <span class="text-sm text-muted-foreground">ID : {{ playerCode ?? player?.code }}</span>
                </div>
            </div>

            <!-- Bouton modifier -->
            <div class="px-4 pb-4">
                <Link :href="route('player.account.infos')" class="flex w-full items-center justify-center gap-2 rounded-2xl border border-border bg-muted/50 py-3.5 text-sm font-medium text-foreground">
                    <Pencil class="h-4 w-4 text-muted-foreground" />
                    Modifier le profil
                </Link>
            </div>


            <!-- Menu Compte -->
            <Card class="mx-4 mt-4 gap-0 py-3 shadow-none border-border/50">
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
                        @click="showLogoutModal = true"
                    >
                        <LogOut class="h-4 w-4" />
                        Se déconnecter
                    </button>

                    <!-- Version -->
                    <p class="mt-3 text-center text-xs text-muted-foreground">MyBAD v1.2.0 · Mars 2026</p>
                </CardContent>
            </Card>
        </div>

        <!-- Modal déconnexion -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showLogoutModal" class="fixed inset-0 z-50 flex items-end justify-center sm:items-center">
                    <div class="absolute inset-0 bg-black/20" @click="showLogoutModal = false" />

                    <div class="relative mx-4 mb-4 w-full max-w-md rounded-3xl bg-white px-6 py-7 shadow-xl">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-50">
                                <LogOut class="h-5 w-5 text-red-500" />
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-foreground">Se déconnecter</h2>
                                <p class="text-sm text-muted-foreground">Êtes-vous sûr de vouloir vous déconnecter ?</p>
                            </div>
                        </div>

                        <div class="mt-5 flex gap-3">
                            <button
                                class="flex-1 rounded-2xl border border-border/50 py-2.5 text-sm font-medium text-foreground"
                                @click="showLogoutModal = false"
                            >
                                Annuler
                            </button>
                            <button
                                class="flex-1 rounded-2xl bg-red-500 py-2.5 text-sm font-semibold text-white"
                                @click="logout"
                            >
                                Déconnexion
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <BottomNavBar active="profil" />
    </PlayerLayout>
</template>
