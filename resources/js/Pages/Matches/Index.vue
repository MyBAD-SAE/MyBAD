<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MatchCard from '@/Components/MatchCard.vue';
import EmptyState from '@/Components/EmptyState.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    matches: Object,
    currentStatus: String,
});

const currentUser = usePage().props.auth.user;

const statuses = [
    { value: null, label: 'Tous' },
    { value: 'pending', label: 'En attente' },
    { value: 'accepted', label: 'En cours' },
    { value: 'completed', label: 'Termines' },
    { value: 'declined', label: 'Refuses' },
    { value: 'cancelled', label: 'Annules' },
];

function filterByStatus(status) {
    router.get(route('matches.index'), { status: status || undefined }, {
        preserveState: true,
        replace: true,
    });
}
</script>

<template>
    <Head title="Matchs" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Matchs</h2>
                <Link :href="route('matches.create')">
                    <PrimaryButton>Nouveau defi</PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Status Filters -->
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="s in statuses"
                        :key="s.value"
                        @click="filterByStatus(s.value)"
                        class="rounded-full px-4 py-1.5 text-sm font-medium transition"
                        :class="currentStatus === s.value ? 'bg-primary-600 text-white' : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50'"
                    >
                        {{ s.label }}
                    </button>
                </div>

                <!-- Matches -->
                <div v-if="matches.data.length > 0" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <MatchCard
                        v-for="match in matches.data"
                        :key="match.id"
                        :match="match"
                        :current-user-id="currentUser.id"
                    />
                </div>
                <EmptyState v-else title="Aucun match" description="Aucun match ne correspond a ce filtre.">
                    <Link :href="route('matches.create')">
                        <PrimaryButton>Defier un joueur</PrimaryButton>
                    </Link>
                </EmptyState>

                <!-- Pagination -->
                <div v-if="matches.links && matches.last_page > 1" class="flex justify-center gap-1">
                    <Link
                        v-for="link in matches.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        v-html="link.label"
                        class="rounded-md px-3 py-1 text-sm"
                        :class="link.active ? 'bg-primary-600 text-white' : link.url ? 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-300' : 'text-gray-400 cursor-default'"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
