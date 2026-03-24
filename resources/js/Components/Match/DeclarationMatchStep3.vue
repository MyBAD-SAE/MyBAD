<script setup>
import { ref, computed } from 'vue'
import { Button } from '@/Components/ui/button'

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
  }
})

const emit = defineEmits(['next', 'back'])

const currentStep = ref(3)
const pin = ref(['', '', '', ''])
const pinRefs = ref([])
const isLoading = ref(false)
const errorMessage = ref('')

const pinComplete = computed(() => pin.value.every(d => d !== ''))

function onPinInput(index, event) {
  errorMessage.value = ''
  const val = event.target.value.replace(/\D/g, '')
  pin.value[index] = val ? val.slice(-1) : ''

  if (val && index < 3) {
    pinRefs.value[index + 1]?.focus()
  }
}

function onPinKeydown(index, event) {
  if (event.key === 'Backspace' && !pin.value[index] && index > 0) {
    pinRefs.value[index - 1]?.focus()
  }
}

function onPinPaste(event) {
  const pasted = event.clipboardData.getData('text').replace(/\D/g, '').slice(0, 4)
  if (pasted.length === 4) {
    pasted.split('').forEach((char, i) => { pin.value[i] = char })
    pinRefs.value[3]?.focus()
  }
  event.preventDefault()
}

async function validatePin() {
  if (!pinComplete.value || isLoading.value) return
  isLoading.value = true
  errorMessage.value = ''

  try {
    // 1. Vérifier le PIN de l'adversaire
    const pinResponse = await axios.post(route('match.verify-pin'), {
      player_id: props.opponent.id,
      pin: pin.value.join('')
    })

    if (!pinResponse.data.valid) {
      errorMessage.value = pinResponse.data.message || 'Code PIN incorrect. Veuillez réessayer.'
      pin.value = ['', '', '', '']
      pinRefs.value[0]?.focus()
      return
    }

    // 2. PIN valide -> soumettre le match
    const matchResponse = await axios.post(route('match.store'), {
      opponent_id: props.opponent.id,
      my_score: props.myScore,
      opponent_score: props.opponentScore,
    })

    if (matchResponse.data.error) {
      errorMessage.value = matchResponse.data.error
      return
    }

    // 3. Succès -> passer à l'étape 4 avec le changement d'ELO
    emit('next', { eloChange: matchResponse.data.eloChange ?? 0 })
  } catch (e) {
    if (e.response?.data?.errors) {
      const firstError = Object.values(e.response.data.errors)[0]
      errorMessage.value = Array.isArray(firstError) ? firstError[0] : firstError
    } else if (e.response?.data?.error) {
      errorMessage.value = e.response.data.error
    } else {
      errorMessage.value = 'Une erreur est survenue. Veuillez réessayer.'
    }
  } finally {
    isLoading.value = false
  }
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

        <p class="text-sm font-bold text-[#352B2B] mb-1 text-center">Validation par l'adversaire</p>

        <!-- Info box -->
        <div class="rounded-xl px-4 py-3 mb-6 mt-3" style="background-color: #ECFDF5; border: 1px solid #e0f2f0;">
          <p class="text-xs text-center leading-5" style="color: #4a5568;">
            Demandez à
            <span class="font-bold" style="color: #27BDAE;">{{ opponent?.name || 'votre adversaire' }}</span>
            d'entrer son code PIN personnel à 4 chiffres pour valider ce match.
          </p>
        </div>

        <!-- PIN inputs -->
        <div class="flex items-center justify-center gap-3 mb-6">
          <input
            v-for="(digit, index) in pin"
            :key="index"
            :ref="el => pinRefs[index] = el"
            v-model="pin[index]"
            type="password"
            inputmode="numeric"
            maxlength="1"
            class="text-center text-xl font-bold text-[#352B2B] bg-white outline-none transition-all"
            style="width: 60px; height: 60px; border-radius: 10px; border: 1.5px solid #e2e8f0;"
            :style="pin[index] ? 'border-color: #27BDAE;' : ''"
            @input="onPinInput(index, $event)"
            @keydown="onPinKeydown(index, $event)"
            @paste="onPinPaste"
          />
        </div>

        <!-- Error message -->
        <p v-if="errorMessage" class="text-xs text-[#D32F2F] text-center mb-4">
          {{ errorMessage }}
        </p>

        <!-- Score recap -->
        <div class="rounded-xl px-6 py-4" style="background-color: #f8fafc;">
          <div class="flex items-center justify-center gap-8">
            <div class="flex flex-col items-center gap-1">
              <span class="text-3xl font-bold text-[#352B2B]">{{ myScore }}</span>
              <span class="text-xs font-semibold text-gray-400 tracking-widest uppercase">
                {{ currentPlayer?.firstName || 'Vous' }}
              </span>
            </div>
            <div class="flex flex-col items-center gap-1">
              <span class="text-3xl font-bold text-[#352B2B]">{{ opponentScore }}</span>
              <span class="text-xs font-semibold text-gray-400 tracking-widest uppercase">
                {{ opponent?.firstName || opponent?.name?.split(' ')[0] || 'Adversaire' }}
              </span>
            </div>
          </div>
        </div>

      </div>

      <!-- Footer button -->
      <div class="px-4 pb-8 pt-2">
        <Button
          @click="validatePin"
          :disabled="!pinComplete || isLoading"
          class="w-full text-base font-semibold gap-2 transition-all shadow-none"
          style="height: 45px; border-radius: 10px;"
          :class="pinComplete && !isLoading
            ? '!bg-[#27BDAE] hover:!bg-[#1fa99b] text-white'
            : '!bg-gray-100 text-gray-400 cursor-not-allowed'"
        >
          <span v-if="isLoading" class="flex items-center gap-2">
            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            Vérification...
          </span>
          <span v-else class="flex items-center gap-2">
            Valider les résultats
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
          </span>
        </Button>
      </div>

    </div>
  </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
</style>
