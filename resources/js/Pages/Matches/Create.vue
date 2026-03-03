<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UserAvatar from '@/Components/UserAvatar.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    players: Array,
});

const currentUser = usePage().props.auth.user;

// Pre-select opponent from query param
const urlParams = new URLSearchParams(window.location.search);
const preselected = urlParams.get('opponent') ? parseInt(urlParams.get('opponent')) : null;

const search = ref('');
const selectedId = ref(preselected);

const filteredPlayers = computed(() => {
    if (!search.value) return props.players;
    return props.players.filter(p =>
        p.name.toLowerCase().includes(search.value.toLowerCase())
    );
});

const selectedPlayer = computed(() =>
    props.players.find(p => p.id === selectedId.value)
);

const form = useForm({
    challenged_id: preselected,
});

function selectPlayer(player) {
    selectedId.value = player.id;
    form.challenged_id = player.id;
}

function submit() {
    form.post(route('matches.store'));
}
</script>

<template>
    <Head title="Nouveau defi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Defier un joueur</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Selected Player -->
                <div v-if="selectedPlayer" class="rounded-xl bg-primary-50 p-6 border border-primary-200 text-center">
                    <UserAvatar :user="selectedPlayer" size="lg" class="mx-auto" />
                    <h3 class="mt-2 text-lg font-semibold text-gray-900">{{ selectedPlayer.name }}</h3>
                    <p class="text-sm text-primary-600">{{ selectedPlayer.elo_rating }} ELO</p>
                </div>

                <!-- Search -->
                <input
                    v-model="search"
                    type="text"
                    placeholder="Rechercher un joueur..."
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                />

                <InputError :message="form.errors.challenged_id" class="mt-2" />

                <!-- Player list -->
                <div class="space-y-2 max-h-80 overflow-y-auto">
                    <button
                        v-for="player in filteredPlayers"
                        :key="player.id"
                        @click="selectPlayer(player)"
                        class="w-full flex items-center gap-3 rounded-lg p-3 text-left transition"
                        :class="selectedId === player.id ? 'bg-primary-50 border-2 border-primary-500' : 'bg-white border border-gray-200 hover:bg-gray-50'"
                    >
                        <UserAvatar :user="player" size="sm" />
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ player.name }}</p>
                            <p class="text-xs text-gray-500">{{ player.elo_rating }} ELO</p>
                        </div>
                        <svg v-if="selectedId === player.id" class="h-5 w-5 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <div class="flex justify-end">
                    <PrimaryButton
                        @click="submit"
                        :disabled="!form.challenged_id || form.processing"
                        :class="{ 'opacity-50': !form.challenged_id }"
                    >
                        Envoyer le defi
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
