<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UserAvatar from '@/Components/UserAvatar.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import ScoreInput from '@/Components/ScoreInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    match: Object,
});

const currentUser = usePage().props.auth.user;

const isChallenger = computed(() => currentUser.id === props.match.challenger_id);
const isChallenged = computed(() => currentUser.id === props.match.challenged_id);
const canSubmitScores = computed(() => props.match.status === 'accepted' && (isChallenger.value || isChallenged.value));
const canAccept = computed(() => props.match.status === 'pending' && isChallenged.value);
const canDecline = computed(() => props.match.status === 'pending' && isChallenged.value);
const canCancel = computed(() => props.match.status === 'pending' && isChallenger.value);

const form = useForm({
    challenger_score_set1: null,
    challenged_score_set1: null,
    challenger_score_set2: null,
    challenged_score_set2: null,
    challenger_score_set3: null,
    challenged_score_set3: null,
});

function submitScores() {
    form.put(route('matches.update', props.match.id));
}

function accept() {
    router.post(route('matches.accept', props.match.id));
}

function decline() {
    router.post(route('matches.decline', props.match.id));
}

function cancel() {
    router.post(route('matches.cancel', props.match.id));
}

function scoreDisplay(set) {
    const cs = props.match[`challenger_score_set${set}`];
    const ds = props.match[`challenged_score_set${set}`];
    if (cs === null) return null;
    return `${cs} - ${ds}`;
}
</script>

<template>
    <Head title="Match" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Detail du match</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Match Header -->
                <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100 text-center">
                    <StatusBadge :status="match.status" class="mb-4" />

                    <div class="flex items-center justify-center gap-8 mt-4">
                        <div class="text-center">
                            <UserAvatar :user="match.challenger" size="lg" class="mx-auto" />
                            <p class="mt-2 text-sm font-semibold" :class="match.winner_id === match.challenger_id ? 'text-primary-600' : 'text-gray-700'">
                                {{ match.challenger.name }}
                            </p>
                            <p class="text-xs text-gray-400">{{ match.challenger.elo_rating }} ELO</p>
                        </div>

                        <span class="text-2xl font-bold text-gray-300">VS</span>

                        <div class="text-center">
                            <UserAvatar :user="match.challenged" size="lg" class="mx-auto" />
                            <p class="mt-2 text-sm font-semibold" :class="match.winner_id === match.challenged_id ? 'text-primary-600' : 'text-gray-700'">
                                {{ match.challenged.name }}
                            </p>
                            <p class="text-xs text-gray-400">{{ match.challenged.elo_rating }} ELO</p>
                        </div>
                    </div>

                    <!-- Completed Scores -->
                    <div v-if="match.status === 'completed'" class="mt-6 space-y-2">
                        <div v-for="set in [1, 2, 3]" :key="set">
                            <p v-if="scoreDisplay(set)" class="text-lg font-mono text-gray-700">
                                Set {{ set }}: {{ scoreDisplay(set) }}
                            </p>
                        </div>
                        <p v-if="match.elo_change" class="text-sm text-gray-500 mt-2">
                            Variation ELO: +/- {{ match.elo_change }}
                        </p>
                        <p v-if="match.winner" class="text-primary-600 font-bold mt-2">
                            Vainqueur : {{ match.winner.name }}
                        </p>
                    </div>
                </div>

                <!-- Actions for pending -->
                <div v-if="match.status === 'pending'" class="rounded-xl bg-white p-6 shadow-sm border border-gray-100">
                    <div v-if="canAccept" class="flex gap-3 justify-center">
                        <button @click="accept" class="rounded-md bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700 transition">
                            Accepter le defi
                        </button>
                        <button @click="decline" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300 transition">
                            Refuser
                        </button>
                    </div>
                    <div v-else-if="canCancel" class="text-center">
                        <p class="text-sm text-gray-500 mb-3">En attente de reponse de {{ match.challenged.name }}...</p>
                        <button @click="cancel" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300 transition">
                            Annuler le defi
                        </button>
                    </div>
                </div>

                <!-- Score Submission for accepted -->
                <div v-if="canSubmitScores" class="rounded-xl bg-white p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 text-center">Saisir les scores</h3>

                    <div class="space-y-4">
                        <div v-for="set in [1, 2, 3]" :key="set" class="flex items-center justify-center gap-4">
                            <span class="text-sm font-medium text-gray-500 w-12">Set {{ set }}</span>
                            <ScoreInput
                                :label="match.challenger.name"
                                v-model="form[`challenger_score_set${set}`]"
                                :error="form.errors[`challenger_score_set${set}`]"
                            />
                            <span class="text-gray-400">-</span>
                            <ScoreInput
                                :label="match.challenged.name"
                                v-model="form[`challenged_score_set${set}`]"
                                :error="form.errors[`challenged_score_set${set}`]"
                            />
                        </div>
                    </div>

                    <p class="text-xs text-gray-400 mt-4 text-center">
                        Best-of-3 sets. Score standard: 21 points, ecart de 2, max 30-29.
                        Ne remplissez le set 3 que si les deux premiers sont a 1-1.
                    </p>

                    <div class="mt-6 flex justify-center">
                        <PrimaryButton @click="submitScores" :disabled="form.processing">
                            Valider les scores
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
