<script setup>
import { ref } from 'vue'
import { Button } from '@/Components/ui/button'
import { router } from '@inertiajs/vue3'
import { TrendingUp, TrendingDown } from 'lucide-vue-next'

const props = defineProps({
  currentPlayer: {
    type: Object,
    default: () => ({ id: 1, name: 'Vous', firstName: 'Vous' })
  },
  opponent: {
    type: Object,
    default: () => ({ id: 2, name: 'Adversaire' })
  },
  myScore: {
    type: Number,
    default: 0
  },
  opponentScore: {
    type: Number,
    default: 0
  },
  eloChange: {
    type: Number,
    default: 0
  }
})

const emit = defineEmits(['restart'])

const currentStep = ref(4)

const isVictory = props.myScore > props.opponentScore

function goToRanking() {
  router.visit(route('classements'))
}

function getInitials(name) {
  return name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase()
}

function getAvatarColor(name) {
  const colors = ['#27BDAE', '#6366f1', '#f59e0b', '#D32F2F', '#8b5cf6', '#10b981', '#f97316', '#3b82f6']
  let hash = 0
  for (let i = 0; i < name.length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash)
  return colors[Math.abs(hash) % colors.length]
}
</script>

<template>
  <div class="min-h-screen bg-white flex flex-col">

      <!-- Header -->
      <div class="px-5 pt-6 pb-4">
        <h1 class="text-lg font-bold text-[#352B2B] text-center">Terminé</h1>
      </div>

      <!-- Stepper — tous validés -->
      <div class="flex items-center justify-center gap-2 px-6 pb-8">
        <template v-for="step in 4" :key="step">
          <div class="w-8 h-8 rounded-full flex items-center justify-center border-[3px] border-[#27BDAE] bg-[#27BDAE]">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
          <div
            v-if="step < 4"
            class="flex-1 h-[3px] rounded-full bg-[#27BDAE]"
          />
        </template>
      </div>

      <!-- Content -->
      <div class="flex-1 flex flex-col items-center px-5">

        <!-- Big check icon -->
        <div class="w-16 h-16 rounded-full flex items-center justify-center mb-5" style="background-color: rgba(39, 189, 174, 0.1);">
          <svg class="w-8 h-8" fill="none" stroke="#27BDAE" viewBox="0 0 24 24" stroke-width="2">
            <circle cx="12" cy="12" r="9" stroke="#27BDAE" stroke-width="2" fill="none"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 12l2.5 2.5L16 9.5"/>
          </svg>
        </div>

        <p class="text-lg font-bold text-[#352B2B] mb-2">Match validé !</p>
        <p class="text-sm text-gray-500 text-center mb-6 leading-5">
          Le résultat a été enregistré et<br>votre classement sera mis à jour.
        </p>

        <!-- Score recap card -->
        <div class="w-full rounded-2xl px-5 py-5 mb-4 border" style="background-color: #F9FAFB; border-color: #F3F4F6;">

          <!-- Players + score row -->
          <div class="flex items-center justify-between mb-4">

            <!-- Current player -->
            <div class="flex flex-col items-center gap-1 w-14">
              <div class="w-12 h-12 rounded-full overflow-hidden shadow-sm">
                <img
                  v-if="currentPlayer?.avatar"
                  :src="currentPlayer.avatar"
                  :alt="currentPlayer.name"
                  class="w-full h-full object-cover"
                />
                <div
                  v-else
                  class="w-full h-full flex items-center justify-center text-white text-sm font-bold"
                  :style="{ backgroundColor: getAvatarColor(currentPlayer?.name || 'Vous') }"
                >
                  {{ getInitials(currentPlayer?.name || 'Vous') }}
                </div>
              </div>
              <span class="font-semibold text-gray-400 tracking-widest uppercase" style="font-size: 9px;">
                {{ currentPlayer?.firstName || 'Vous' }}
              </span>
            </div>

            <!-- Score -->
            <div class="flex items-center gap-2">
              <span class="text-4xl font-bold leading-none" :style="myScore >= opponentScore ? 'color: #27BDAE;' : 'color: #9ca3af;'">
                {{ String(myScore).padStart(2, '0') }}
              </span>
              <span class="font-light text-gray-300 text-2xl">-</span>
              <span class="text-4xl font-bold leading-none" :style="opponentScore >= myScore ? 'color: #27BDAE;' : 'color: #9ca3af;'">
                {{ String(opponentScore).padStart(2, '0') }}
              </span>
            </div>

            <!-- Opponent -->
            <div class="flex flex-col items-center gap-1 w-14">
              <div class="w-12 h-12 rounded-full overflow-hidden shadow-sm">
                <img
                  v-if="opponent?.avatar"
                  :src="opponent.avatar"
                  :alt="opponent.name"
                  class="w-full h-full object-cover"
                />
                <div
                  v-else
                  class="w-full h-full flex items-center justify-center text-white text-sm font-bold"
                  :style="{ backgroundColor: getAvatarColor(opponent?.name || 'ADV') }"
                >
                  {{ getInitials(opponent?.name || 'ADV') }}
                </div>
              </div>
              <span class="font-semibold text-gray-400 tracking-widest uppercase" style="font-size: 9px;">
                {{ opponent?.firstName || opponent?.name?.split(' ')[0] || 'Adversaire' }}
              </span>
            </div>

          </div>

          <!-- Badges centrés -->
          <div class="flex items-center justify-center gap-2">
            <span
              class="text-[10px] font-bold px-2.5 py-1 rounded-full"
              :style="isVictory
                ? 'color: #009966; background-color: #ECFDF5;'
                : 'color: #D32F2F; background-color: #FEF2F2;'"
            >
              {{ isVictory ? 'VICTOIRE' : 'DÉFAITE' }}
            </span>
            <span
              v-if="eloChange !== 0"
              class="text-[10px] font-bold px-2.5 py-1 rounded-full flex items-center gap-1"
              :style="eloChange > 0
                ? 'color: #009966; background-color: #ECFDF5;'
                : 'color: #D32F2F; background-color: #FEF2F2;'"
            >
              <TrendingUp v-if="eloChange > 0" class="w-3 h-3" />
              <TrendingDown v-else class="w-3 h-3" />
              {{ eloChange > 0 ? '+' : '' }}{{ eloChange }} ELO
            </span>
          </div>

        </div>

        <!-- Déclarer un autre match -->
        <button
          @click="$emit('restart')"
          class="flex items-center gap-2 text-sm text-gray-400 hover:text-gray-600 transition-colors mt-2"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Déclarer un autre match
        </button>

      </div>

      <!-- Footer button -->
      <div class="px-5 pb-8 pt-2">
        <Button
          @click="goToRanking"
          class="w-full text-sm font-semibold gap-2 transition-all shadow-none !bg-[#27BDAE] hover:!bg-[#1fa99b] text-white"
          style="height: 48px; border-radius: 16px;"
        >
          Voir le classement
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
          </svg>
        </Button>
      </div>

  </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
</style>