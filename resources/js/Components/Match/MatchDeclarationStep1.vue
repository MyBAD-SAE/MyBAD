<script setup>
import { ref, computed, onMounted } from 'vue'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { ArrowRight } from 'lucide-vue-next'

const props = defineProps({
  currentPlayer: {
    type: Object,
    required: true,
  },
  selectedOpponent: {
    type: Object,
    default: null
  },
  activeSession: {
    type: [Number, String],
    default: null
  }
})

const emit = defineEmits(['next', 'back'])

const currentStep = ref(1)
const searchQuery = ref('')
const selectedPlayer = ref(props.selectedOpponent)
const players = ref([])
const currentElo = ref(0)
const isLoading = ref(false)
const errorMessage = ref('')

onMounted(async () => {
  await fetchOpponents()
})

async function fetchOpponents() {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const response = await axios.get(route('match.opponents'))
    if (response.data.error) {
      errorMessage.value = response.data.error
    }
    players.value = response.data.opponents || []
    currentElo.value = response.data.currentElo || 0
  } catch (e) {
    errorMessage.value = 'Impossible de charger la liste des adversaires.'
  } finally {
    isLoading.value = false
  }
}

const closestPlayers = computed(() => {
  const available = players.value.filter(p => !p.already_played)
  const sorted = [...available].sort((a, b) => Math.abs(a.elo - currentElo.value) - Math.abs(b.elo - currentElo.value))
  const below = sorted.filter(p => p.elo <= currentElo.value).slice(0, 4)
  const above = sorted.filter(p => p.elo > currentElo.value).slice(0, 4)
  const ids = new Set([...below, ...above].map(p => p.id))
  if (ids.size < 8) {
    for (const p of sorted) {
      if (ids.size >= 8) break
      ids.add(p.id)
    }
  }
  // Si on n'a pas 8 joueurs disponibles, compléter avec les déjà joués
  if (ids.size < 8) {
    const played = players.value.filter(p => p.already_played)
      .sort((a, b) => Math.abs(a.elo - currentElo.value) - Math.abs(b.elo - currentElo.value))
    for (const p of played) {
      if (ids.size >= 8) break
      ids.add(p.id)
    }
  }
  return ids
})

const filteredPlayers = computed(() => {
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    return players.value.filter(p => p.name.toLowerCase().includes(q))
  }
  return players.value.filter(p => closestPlayers.value.has(p.id))
})

function selectPlayer(player) {
  if (player.already_played) return
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
  <div class="h-dvh bg-white flex flex-col overflow-hidden">

      <!-- Header -->
      <div class="px-5 pt-6 pb-4 flex items-center gap-3">
        <button
          @click="$emit('back')"
          class="w-9 h-9 rounded-xl flex items-center justify-center hover:bg-gray-50 transition-colors shrink-0"
          style="background-color: #ffffff; border: 1px solid #e5e7eb;"
        >
          <svg class="w-4 h-4 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>
        <h1 class="text-lg font-bold text-[#352B2B] flex-1 text-center pr-11">Déclarer un match</h1>
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
      <div class="flex-1 min-h-0 flex flex-col px-5">
        <p class="text-sm font-bold text-gray-700 mb-0.5">Sélectionnez le joueur défié</p>
        <p class="text-xs text-gray-400 mb-2">Recherchez votre adversaire dans la liste ci-dessous</p>

        <!-- Error message -->
        <div v-if="errorMessage" class="rounded-xl px-4 py-3 mb-4" style="background-color: #FEF2F2; border: 1px solid #fecaca;">
          <p class="text-xs text-center text-[#D32F2F]">{{ errorMessage }}</p>
        </div>

        <!-- Loading -->
        <div v-if="isLoading" class="flex-1 flex items-center justify-center">
          <svg class="w-6 h-6 animate-spin text-[#27BDAE]" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
          </svg>
        </div>

        <template v-else>
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
          <div class="flex-1 min-h-0 overflow-y-auto space-y-2 pb-4">
            <Button
              v-for="player in filteredPlayers"
              :key="player.id"
              variant="outline"
              @click="selectPlayer(player)"
              :disabled="player.already_played"
              class="w-full flex items-center gap-3 px-3 transition-all justify-start"
              style="height: 58px; border-radius: 10px;"
              :class="player.already_played
                ? 'bg-gray-50 opacity-50 cursor-not-allowed border border-[#F3F4F6] !shadow-none'
                : selectedPlayer?.id === player.id
                  ? 'border border-[#27BDAE] bg-[#27BDAE]/5 hover:bg-[#27BDAE]/5 !shadow-none'
                  : 'bg-white border border-[#F3F4F6] hover:border-gray-200 hover:bg-gray-50 !shadow-none'"
            >
              <div
                class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0 overflow-hidden"
                :style="{ backgroundColor: player.avatar ? 'transparent' : getAvatarColor(player.name), filter: player.already_played ? 'grayscale(100%)' : 'none' }"
              >
                <img v-if="player.avatar" :src="player.avatar" :alt="player.name" class="w-full h-full object-cover" />
                <span v-else>{{ getInitials(player.name) }}</span>
              </div>
              <div class="flex-1 min-w-0 text-left">
                <p class="text-sm font-semibold truncate" :class="player.already_played ? 'text-gray-400' : 'text-[#352B2B]'">{{ player.name }}</p>
                <p class="text-xs font-normal" :class="player.already_played ? 'text-gray-300' : 'text-gray-400'">
                  {{ player.already_played ? 'Déjà affronté' : `ELO ${player.elo}` }}
                </p>
              </div>
              <div
                v-if="selectedPlayer?.id === player.id && !player.already_played"
                class="w-6 h-6 rounded-full bg-[#27BDAE] flex items-center justify-center flex-shrink-0"
              >
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
              </div>
            </Button>

            <p v-if="filteredPlayers.length === 0 && !isLoading" class="text-center text-sm text-gray-400 py-6">
              Aucun joueur trouvé
            </p>
          </div>
        </template>
      </div>

      <!-- Footer button -->
      <div class="px-5 pb-8 pt-2">
        <Button
          @click="goToNext"
          :disabled="!selectedPlayer"
          class="w-full text-sm font-semibold gap-2 transition-all shadow-none"
          style="height: 44px; border-radius: 16px;"
          :class="selectedPlayer
            ? '!bg-[#27BDAE] hover:!bg-[#1fa99b] text-white'
            : '!bg-gray-100 text-gray-400 cursor-not-allowed'"
        >
          Continuer
          <ArrowRight class="w-4 h-4" />
        </Button>
      </div>

  </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
</style>
