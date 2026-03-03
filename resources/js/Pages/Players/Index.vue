<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PlayerCard from '@/Components/PlayerCard.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    players: Array,
    search: String,
});

const currentUser = usePage().props.auth.user;
const searchQuery = ref(props.search || '');

let timeout = null;
watch(searchQuery, (val) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route('players.index'), { search: val || undefined }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});
</script>

<template>
    <Head title="Joueurs" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Joueurs</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Search -->
                <div>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Rechercher un joueur..."
                        class="w-full max-w-md rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    />
                </div>

                <!-- Players Grid -->
                <div v-if="players.length > 0" class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    <PlayerCard
                        v-for="player in players"
                        :key="player.id"
                        :player="player"
                        :current-user-id="currentUser.id"
                    />
                </div>
                <EmptyState v-else title="Aucun joueur trouve" description="Essayez avec un autre nom." />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
