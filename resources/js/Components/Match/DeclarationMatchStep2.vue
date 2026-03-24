<script setup>
import {ref, computed} from 'vue'
import {Button} from '@/Components/ui/button'
import {Input} from '@/Components/ui/input'
import {Swords, ArrowRight} from 'lucide-vue-next'

const props = defineProps({
    currentPlayer: {
        type: Object,
        default: () => ({id: 1, name: 'Vous', firstName: 'Vous'})
    },
    opponent: {
        type: Object,
        default: () => ({id: 2, name: 'Adversaire', firstName: 'Adversaire'})
    }
})

const emit = defineEmits(['next', 'back'])

const currentStep = ref(2)
const myScore = ref(null)
const opponentScore = ref(null)
const myScoreRef = ref(null)
const opponentScoreRef = ref(null)

const scoreError = ref('')
let myScoreTimer = null

const isValid = computed(() =>
    myScore.value !== null && opponentScore.value !== null &&
    myScore.value !== '' && opponentScore.value !== ''
)

const hasMinScoreError = computed(() => scoreError.value !== '')

function focusInput(field) {
    if (field === 'myScore') myScoreRef.value?.focus()
    else opponentScoreRef.value?.focus()
}

function focusOpponent() {
    const el = opponentScoreRef.value?.$el || opponentScoreRef.value
    el?.focus()
}

function clampScore(field) {
    if (field === 'myScore') {
        if (myScore.value > 99) myScore.value = 99
        if (myScore.value < 0) myScore.value = 0
        clearTimeout(myScoreTimer)
        if (String(myScore.value).length >= 2) {
            focusOpponent()
        } else if (myScore.value !== null && myScore.value !== '') {
            myScoreTimer = setTimeout(focusOpponent, 800)
        }
    } else {
        if (opponentScore.value > 99) opponentScore.value = 99
        if (opponentScore.value < 0) opponentScore.value = 0
    }
}

function goToNext() {
    if (!isValid.value) return
    if (Number(myScore.value) < 15 && Number(opponentScore.value) < 15) {
        scoreError.value = 'Au moins un des deux scores doit être de 15 points minimum.'
        return
    }
    scoreError.value = ''
    emit('next', {
        myScore: Number(myScore.value),
        opponentScore: Number(opponentScore.value)
    })
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
            <div class="px-4 pt-6 pb-4 flex items-center gap-3">
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
                        <svg v-if="step < currentStep" class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
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
                <p class="text-sm font-bold text-[#352B2B] mb-1 text-center">Saisissez le score</p>
                <p class="text-xs text-gray-400 mb-6 text-center">Entrez le score final de la rencontre.</p>

                <!-- Score card -->
                <div class="rounded-2xl py-5 px-8 border" style="background-color: #F9FAFB; border-color: #F3F4F6;">

                    <div class="flex items-start justify-between">

                        <!-- Colonne joueur gauche -->
                        <div class="flex flex-col items-center gap-3 px-4">
                            <div class="w-14 h-14 rounded-full overflow-hidden border-2 border-white shadow-sm">
                                <img
                                    v-if="currentPlayer?.avatar"
                                    :src="currentPlayer.avatar"
                                    :alt="currentPlayer.name"
                                    class="w-full h-full object-cover"
                                />
                                <div
                                    v-else
                                    class="w-full h-full flex items-center justify-center text-white text-lg font-bold"
                                    :style="{ backgroundColor: getAvatarColor(currentPlayer?.name || 'Vous') }"
                                >
                                    {{ getInitials(currentPlayer?.name || 'Vous') }}
                                </div>
                            </div>
                            <span class="text-xs font-semibold text-gray-500 tracking-wide uppercase">
                {{ currentPlayer?.firstName || 'Vous' }}
              </span>
                            <!-- Input score gauche -->
                            <Input
                                ref="myScoreRef"
                                v-model="myScore"
                                type="number"
                                min="0"
                                max="99"
                                :aria-invalid="hasMinScoreError"
                                class="!w-14 !h-14 text-center font-bold text-[#352B2B] !bg-white text-2xl !rounded-[10px] !shadow-none !px-0 !border-[#E5E7EB]"
                                style="font-family: 'Poppins', sans-serif;"
                                @input="clampScore('myScore')"
                            />
                        </div>

                        <!-- VS centré -->
                        <div class="flex flex-col items-center gap-0.5 pt-4">
                            <Swords class="w-4 h-4" style="color: #99a1af;" />
                            <span class="text-xs font-semibold text-gray-400 tracking-widest">VS</span>
                        </div>

                        <!-- Colonne joueur droite -->
                        <div class="flex flex-col items-center gap-3 px-4">
                            <div class="w-14 h-14 rounded-full overflow-hidden border-2 border-white shadow-sm">
                                <img
                                    v-if="opponent?.avatar"
                                    :src="opponent.avatar"
                                    :alt="opponent.name"
                                    class="w-full h-full object-cover"
                                />
                                <div
                                    v-else
                                    class="w-full h-full flex items-center justify-center text-white text-lg font-bold"
                                    :style="{ backgroundColor: getAvatarColor(opponent?.name || 'Adversaire') }"
                                >
                                    {{ getInitials(opponent?.name || 'ADV') }}
                                </div>
                            </div>
                            <span class="text-xs font-semibold text-gray-500 tracking-wide uppercase">
                {{ opponent?.firstName || opponent?.name?.split(' ')[0] || 'Adversaire' }}
              </span>
                            <!-- Input score droite -->
                            <Input
                                ref="opponentScoreRef"
                                v-model="opponentScore"
                                type="number"
                                min="0"
                                max="99"
                                :aria-invalid="hasMinScoreError"
                                class="!w-14 !h-14 text-center font-bold text-[#352B2B] !bg-white text-2xl !rounded-[10px] !shadow-none !px-0 !border-[#E5E7EB]"
                                style="font-family: 'Poppins', sans-serif;"
                                @input="clampScore('opponentScore')"
                            />
                        </div>

                    </div>
                </div>

                <!-- Message d'erreur score minimum -->
                <div v-if="hasMinScoreError" class="rounded-2xl px-4 py-2.5 mt-4 text-center border" style="background-color: rgba(211, 47, 47, 0.07); border-color: rgba(211, 47, 47, 0.2);">
                    <p class="text-xs font-normal" style="color: #D32F2F;">{{ scoreError }}</p>
                </div>
            </div>

            <!-- Footer button -->
            <div class="px-4 pb-8 pt-2">
                <Button
                    @click="goToNext"
                    :disabled="!isValid"
                    class="w-full text-sm font-semibold gap-2 transition-all shadow-none"
                    style="height: 48px; border-radius: 16px;"
                    :class="isValid
            ? '!bg-[#27BDAE] hover:!bg-[#1fa99b] text-white'
            : '!bg-gray-100 text-gray-400 cursor-not-allowed'"
                >
                    Continuer
                    <ArrowRight class="w-4 h-4" />
                </Button>
            </div>

        </div>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

input[type='number']::-webkit-inner-spin-button,
input[type='number']::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type='number'] {
    -moz-appearance: textfield;
}
</style>
