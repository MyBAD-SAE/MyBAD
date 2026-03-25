<script setup>
import { ref } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import DeclarationMatchStep1 from '@/Components/Match/MatchDeclarationStep1.vue'
import DeclarationMatchStep2 from '@/Components/Match/MatchDeclarationStep2.vue'
import DeclarationMatchStep3 from '@/Components/Match/MatchDeclarationStep3.vue'
import DeclarationMatchStep4 from '@/Components/Match/MatchDeclarationStep4.vue'

const page = usePage()

const currentPlayer = ref(page.props.currentPlayer)
const activeSession = ref(page.props.activeSession)

const currentStep = ref(1)
const matchData = ref({
  opponent: null,
  myScore: null,
  opponentScore: null,
  eloChange: 0,
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
  currentStep.value = 4
}

function handleRestart() {
  matchData.value = {
    opponent: null,
    myScore: null,
    opponentScore: null,
    eloChange: 0,
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
      @restart="handleRestart"
    />
</template>
