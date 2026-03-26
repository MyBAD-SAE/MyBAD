<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AdminClassPicker from '@/Components/admin/AdminClassPicker.vue';
import AdminActionCard from '@/Components/admin/AdminActionCard.vue';
import AdminRankingCard from '@/Components/admin/AdminRankingCard.vue';
import { Play, Users, Swords, Trophy } from 'lucide-vue-next';

defineProps({
    classes: { type: Array, default: () => [] },
    selectedClassId: { type: Number, default: null },
    playerCount: { type: Number, default: 0 },
    matchCount: { type: Number, default: 0 },
    rankingPlayers: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="Dashboard Admin" />

    <AdminLayout>
        <div class="p-8 space-y-8">
            <!-- Gestion section -->
            <div>
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-2xl font-bold text-foreground">Gestion</h2>
                        <p class="mt-1 text-sm text-muted-foreground">Actions rapides administrateur</p>
                    </div>
                    <AdminClassPicker :classes="classes" :selected-class-id="selectedClassId" />
                </div>

                <!-- Action cards -->
                <div class="grid grid-cols-4 gap-4">
                    <AdminActionCard
                        title="Nouvelle seance"
                        description="Lancer une session de jeu pour le groupe"
                        :icon="Play"
                        icon-bg-class="bg-primary/10"
                        icon-color-class="text-primary"
                    />
                    <AdminActionCard
                        title="Joueurs"
                        description="Gerer les joueurs et leurs profils"
                        :icon="Users"
                        :badge="playerCount"
                        :href="route('admin.joueurs')"
                        icon-bg-class="bg-violet-100"
                        icon-color-class="text-violet-500"
                    />
                    <AdminActionCard
                        title="Matchs"
                        description="Historique et validation des matchs"
                        :icon="Swords"
                        :badge="matchCount"
                        :href="route('admin.matchs')"
                        icon-bg-class="bg-orange-100"
                        icon-color-class="text-orange-500"
                    />
                    <AdminActionCard
                        title="Regles et defis"
                        description="Configurer les regles et creer des defis"
                        :icon="Trophy"
                        :href="route('admin.regles')"
                        icon-bg-class="bg-red-100"
                        icon-color-class="text-red-500"
                    />
                </div>
            </div>

            <!-- Classement section -->
            <AdminRankingCard :players="rankingPlayers" :player-count="playerCount" />
        </div>
    </AdminLayout>
</template>
