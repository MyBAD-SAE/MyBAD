<script setup>
import { ref, computed } from 'vue'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'

const props = defineProps({
  currentPlayer: {
    type: Object,
    default: () => ({ id: 1, name: 'Moi' })
  },
  selectedOpponent: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['next', 'back'])

const currentStep = ref(1)
const searchQuery = ref('')
const selectedPlayer = ref(props.selectedOpponent)

// TODO: Récupérer la liste des joueurs depuis l'API, en excluant le joueur connecté
// Pour l'instant, on met une liste statique pour les tests
const players = ref([
  { id: 2, name: 'Quentin UGUEN', elo: 1425 },
  { id: 3, name: 'Amélie DUBOIS', elo: 1318 },
  { id: 4, name: 'Kenji TANAKA', elo: 1350 },
  { id: 5, name: 'Clara MARTIN', elo: 1315 },
  { id: 6, name: 'Kenji TANAKA', elo: 1420 },
  { id: 7, name: 'Quentin UGUEN', elo: 1425 },
  { id: 8, name: 'Amélie DUBOIS', elo: 1318 },
])

const filteredPlayers = computed(() => {
  if (!searchQuery.value.trim()) return players.value
  const q = searchQuery.value.toLowerCase()
  return players.value.filter(p => p.name.toLowerCase().includes(q))
})

function selectPlayer(player) {
  selectedPlayer.value = player
}

function goToNext() {
  if (!selectedPlayer.value) return
  emit('next', { opponent: selectedPlayer.value })
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
  <div class="min-h-screen bg-white flex items-center justify-center" style="font-family: 'Poppins', sans-serif;">
    <div class="w-full max-w-sm min-h-screen bg-white flex flex-col">

      <!-- Header -->
      <div class="px-4 pt-6 pb-4">
        <button
          @click="$emit('back')"
          class="mb-4 w-11 h-11 rounded-2xl flex items-center justify-center hover:bg-gray-50 transition-colors"
          style="background-color: #ffffff; border: 1px solid #e5e7eb;"
        >
          <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>
        <h1 class="text-lg font-bold text-[#352B2B] text-center -mt-6">Déclarer un match</h1>
      </div>

      <!-- Stepper -->
      <div class="flex items-center justify-center gap-2 px-6 pb-6">
        <template v-for="step in 4" :key="step">
          <div
            class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold transition-all border-[3px]"
            :class="step < currentStep
              ? 'border-[#27BDAE] bg-[#27BDAE] text-white'
              : step === currentStep
                ? 'border-[#27BDAE] text-[#27BDAE] bg-white'
                : 'border-gray-300 text-gray-300 bg-white'"
          >
            <svg v-if="step < currentStep" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            <span v-else>{{ step }}</span>
          </div>
          <div
            v-if="step < 4"
            class="flex-1 h-[3px] rounded-full transition-all"
            :class="step < currentStep ? 'bg-[#27BDAE]' : 'bg-gray-200'"
          />
        </template>
      </div>

      <!-- Content -->
      <div class="flex-1 flex flex-col px-4">
        <p class="text-sm font-bold text-gray-700 mb-1">Sélectionnez le joueur défié</p>
        <p class="text-xs text-gray-400 mb-3">Recherchez votre adversaire dans la liste ci-dessous</p>

        <!-- Search -->
        <div class="relative mb-4">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
          </svg>
          <Input
            v-model="searchQuery"
            type="text"
            placeholder="Rechercher un joueur..."
            class="pl-9 w-full text-sm placeholder:text-gray-400 shadow-none focus-visible:ring-1 focus-visible:ring-[#27BDAE]"
            style="height: 36px; border-radius: 10px; background-color: #f8fafc; border: 1px solid #e2e8f0;"
          />
        </div>

        <!-- Player list -->
        <div class="flex-1 overflow-y-auto space-y-2 pb-4">
          <Button
            v-for="player in filteredPlayers"
            :key="player.id"
            variant="outline"
            @click="selectPlayer(player)"
            class="w-full flex items-center gap-3 px-3 border transition-all justify-start"
            style="height: 52px; border-radius: 10px;"
            :class="selectedPlayer?.id === player.id
              ? 'border-[#27BDAE] bg-[#ECFDF5] hover:bg-[#ECFDF5] shadow-sm'
              : 'border-gray-100 hover:border-gray-200 hover:bg-gray-50'"
          >
            <div
              class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
              :style="{ backgroundColor: getAvatarColor(player.name) }"
            >
              {{ getInitials(player.name) }}
            </div>
            <div class="flex-1 min-w-0 text-left">
              <p class="text-sm font-semibold text-[#352B2B] truncate">{{ player.name }}</p>
              <p class="text-xs text-gray-400 font-normal">ELO {{ player.elo }}</p>
            </div>
            <div
              v-if="selectedPlayer?.id === player.id"
              class="w-6 h-6 rounded-full bg-[#27BDAE] flex items-center justify-center flex-shrink-0"
            >
              <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
              </svg>
            </div>
          </Button>

          <p v-if="filteredPlayers.length === 0" class="text-center text-sm text-gray-400 py-6">
            Aucun joueur trouvé
          </p>
        </div>
      </div>

      <!-- Footer button -->
      <div class="px-4 pb-8 pt-2">
        <Button
          @click="goToNext"
          :disabled="!selectedPlayer"
          class="w-full text-base font-semibold gap-2 transition-all shadow-none"
          style="height: 45px; border-radius: 10px;"
          :class="selectedPlayer
            ? '!bg-[#27BDAE] hover:!bg-[#1fa99b] text-white'
            : '!bg-gray-100 text-gray-400 cursor-not-allowed'"
        >
          Continuer
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
          </svg>
        </Button>
      </div>

    </div>
  </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
</style>