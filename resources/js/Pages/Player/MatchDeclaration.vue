<script setup>
import { ref } from 'vue'
import { Head, usePage, router } from '@inertiajs/vue3'
import PlayerLayout from '@/Layouts/PlayerLayout.vue'
import DeclarationMatchStep1 from '@/Components/player/Match/MatchDeclarationStep1.vue'
import DeclarationMatchStep2 from '@/Components/player/Match/MatchDeclarationStep2.vue'
import DeclarationMatchStep3 from '@/Components/player/Match/MatchDeclarationStep3.vue'
import DeclarationMatchStep4 from '@/Components/player/Match/MatchDeclarationStep4.vue'

const page = usePage()

const currentPlayer = ref(page.props.currentPlayer)
const activeSession = ref(page.props.activeSession)

const currentStep = ref(1)
const matchData = ref({
  opponent: null,
  myScore: null,
  opponentScore: null,
  eloChange: 0,
  eloChangeOpponent: 0,
})

function handleStep1(data) {
  matchData.value.opponent = data.opponent
  currentStep.value = 2
}

function handleStep2(data) {
  matchData.value.myScore = data.myScore
  matchData.value.opponentScore = data.opponentScore
  currentStep.value = 3
}

function handleStep3(data) {
  matchData.value.eloChange = data?.eloChange ?? 0
  matchData.value.eloChangeOpponent = data?.eloChangeOpponent ?? 0
  currentStep.value = 4
}

function handleRestart() {
  matchData.value = {
    opponent: null,
    myScore: null,
    opponentScore: null,
    eloChange: 0,
    eloChangeOpponent: 0,
  }
  currentStep.value = 1
}

function handleBack() {
  if (currentStep.value > 1) currentStep.value--
  else window.history.back()
}
</script>

<template>
    <Head title="Déclarer un match" />

    <PlayerLayout>
    <!-- Pas de séance active -->
    <div v-if="!activeSession" class="h-dvh flex flex-col items-center justify-center px-8 text-center bg-white">
      <div class="w-16 h-16 rounded-full flex items-center justify-center mb-5 bg-gray-100">
        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
      </div>
      <h2 class="text-lg font-bold text-[#352B2B] mb-2">Aucune séance active</h2>
      <p class="text-sm text-gray-400 leading-5">La déclaration de match n'est disponible que pendant une séance ouverte par votre professeur.</p>
      <button
        @click="router.visit(route('home'))"
        class="mt-8 text-sm font-semibold text-[#27BDAE]"
      >
        Retour au dashboard
      </button>
    </div>

    <template v-else>
    <DeclarationMatchStep1
      v-if="currentStep === 1"
      :current-player="currentPlayer"
      :selected-opponent="matchData.opponent"
      :active-session="activeSession"
      @next="handleStep1"
      @back="handleBack"
    />

    <DeclarationMatchStep2
      v-else-if="currentStep === 2"
      :current-player="currentPlayer"
      :opponent="matchData.opponent"
      @next="handleStep2"
      @back="handleBack"
    />

    <DeclarationMatchStep3
      v-else-if="currentStep === 3"
      :current-player="currentPlayer"
      :opponent="matchData.opponent"
      :my-score="matchData.myScore"
      :opponent-score="matchData.opponentScore"
      @next="handleStep3"
      @back="handleBack"
    />

    <DeclarationMatchStep4
      v-else-if="currentStep === 4"
      :current-player="currentPlayer"
      :opponent="matchData.opponent"
      :my-score="matchData.myScore"
      :opponent-score="matchData.opponentScore"
      :elo-change="matchData.eloChange"
      :elo-change-opponent="matchData.eloChangeOpponent"
      @restart="handleRestart"
    />
    </template>
    </PlayerLayout>
</template>
