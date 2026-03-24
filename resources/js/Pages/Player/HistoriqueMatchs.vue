<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import PlayerLayout from '@/Layouts/PlayerLayout.vue'
import BottomNavBar from '@/Components/BottomNavBar.vue'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Tabs, TabsList, TabsTrigger } from '@/Components/ui/tabs'
import { Trophy, Frown } from 'lucide-vue-next'

// ─────────────────────────────────────────────
// TODO: Remplacer par usePage().props.auth.player via Inertia
// ─────────────────────────────────────────────
const currentPlayer = ref({
  id: 1,
  name: 'Vous',
  firstName: 'Vous',
})

// ─────────────────────────────────────────────
// TODO: Remplacer par un appel API Laravel :
// GET /api/player/matches
// Retourne la liste des matchs du joueur connecté
// avec : id, opponent (name, avatar), myScore,
// opponentScore, eloChange, date, result ('win'|'loss')
// ─────────────────────────────────────────────
const matches = ref([
  { id: 1,  opponent: { name: 'Quentin UGUEN',  avatar: null }, myScore: 15, opponentScore: 7,  eloChange: +15, date: '10 mar.', result: 'win'  },
  { id: 2,  opponent: { name: 'Amélie DUBOIS',  avatar: null }, myScore: 15, opponentScore: 4,  eloChange: +22, date: '10 mar.', result: 'win'  },
  { id: 3,  opponent: { name: 'Kenji TANAKA',   avatar: null }, myScore: 6,  opponentScore: 15, eloChange: -12, date: '10 mar.', result: 'loss' },
  { id: 4,  opponent: { name: 'Clara MARTIN',   avatar: null }, myScore: 15, opponentScore: 12, eloChange: +8,  date: '10 mar.', result: 'win'  },
  { id: 5,  opponent: { name: 'Hugo LEROUX',    avatar: null }, myScore: 4,  opponentScore: 15, eloChange: -18, date: '10 mar.', result: 'loss' },
  { id: 6,  opponent: { name: 'Maxime LEROY',   avatar: null }, myScore: 15, opponentScore: 9,  eloChange: +11, date: '3 mar.',  result: 'win'  },
  { id: 7,  opponent: { name: 'Sophie BERNARD', avatar: null }, myScore: 7,  opponentScore: 15, eloChange: -15, date: '3 mar.',  result: 'loss' },
  { id: 8,  opponent: { name: 'Thomas PETIT',   avatar: null }, myScore: 17, opponentScore: 15, eloChange: +6,  date: '3 mar.',  result: 'win'  },
  { id: 9,  opponent: { name: 'Emma ROUX',      avatar: null }, myScore: 15, opponentScore: 3,  eloChange: +19, date: '3 mar.',  result: 'win'  },
  { id: 10, opponent: { name: 'Hugo GARNIER',   avatar: null }, myScore: 15, opponentScore: 2,  eloChange: -20, date: '3 mar.',  result: 'loss' },
])

// État des filtres
const activeTab   = ref('all')   // 'all' | 'win' | 'loss'
const searchQuery = ref('')

// Compteurs dynamiques
const winsCount   = computed(() => matches.value.filter(m => m.result === 'win').length)
const lossesCount = computed(() => matches.value.filter(m => m.result === 'loss').length)

// Filtrage dynamique
const filteredMatches = computed(() => {
  let list = matches.value

  if (activeTab.value === 'win')  list = list.filter(m => m.result === 'win')
  if (activeTab.value === 'loss') list = list.filter(m => m.result === 'loss')

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(m => m.opponent.name.toLowerCase().includes(q))
  }

  return list
})

// Navigation
function goBack() {
  router.visit(route('home'))
}

function goToNewMatch() {
  router.visit(route('match.declare'))
}

// Helpers avatar
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
  <PlayerLayout>
  <div class="min-h-screen bg-white flex items-center justify-center" style="font-family: 'Poppins', sans-serif;">
    <div class="w-full max-w-sm min-h-screen bg-white flex flex-col pb-16">

      <!-- Header -->
      <div class="px-4 pt-6 pb-8 relative flex items-center justify-center">
        <button
          @click="goBack"
          class="absolute left-4 w-9 h-9 rounded-xl flex items-center justify-center hover:bg-gray-50 transition-colors"
          style="background-color: #ffffff; border: 1px solid #e5e7eb;"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #352B2B;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>
        <h1 class="text-lg font-bold" style="color: #352B2B;">Historique des matchs</h1>
      </div>

      <!-- Content -->
      <div class="flex-1 flex flex-col px-4 pb-4">

        <!-- Victoires / Défaites / Nouveau — même ligne -->
        <div class="flex items-center gap-3 mb-4">
          <div
            class="flex items-center gap-1.5 px-2.5 h-8"
            style="background-color: #ECFDF5; border: 1px solid #D0FAE5; border-radius: 14px;"
          >
            <Trophy class="w-4 h-4 text-emerald-600" />
            <span class="text-xs font-bold text-emerald-600">{{ winsCount }}</span>
          </div>

          <div
            class="flex items-center gap-1.5 px-2.5 h-8"
            style="background-color: #FEF2F2; border: 1px solid #FFE2E2; border-radius: 14px;"
          >
            <Frown class="w-4 h-4 text-red-600" />
            <span class="text-xs font-bold text-red-600">{{ lossesCount }}</span>
          </div>

          <div class="flex-1" />

          <Button
            @click="goToNewMatch"
            class="!bg-[#27BDAE] hover:!bg-[#1fa99b] text-white shadow-none font-semibold text-xs gap-1 h-8 px-3"
            style="border-radius: 14px;"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau
          </Button>
        </div>

        <!-- Barre de recherche -->
        <div class="relative mb-4">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
          </svg>
          <Input
            v-model="searchQuery"
            type="text"
            placeholder="Rechercher un adversaire..."
            class="pl-9 w-full text-sm placeholder:text-gray-400 shadow-none focus-visible:ring-1 focus-visible:ring-[#27BDAE]"
            style="height: 36px; border-radius: 10px; background-color: #f8fafc; border: 1px solid #e2e8f0;"
          />
        </div>

        <!-- Tabs filtre -->
        <Tabs v-model="activeTab" class="mb-4">
          <TabsList class="grid w-full grid-cols-3">
            <TabsTrigger value="all">Tous</TabsTrigger>
            <TabsTrigger value="win">Victoires</TabsTrigger>
            <TabsTrigger value="loss">Défaites</TabsTrigger>
          </TabsList>
        </Tabs>

        <!-- Liste des matchs -->
        <div class="flex-1 overflow-y-auto pb-4">
          <div class="space-y-2">

          <div
            v-for="match in filteredMatches"
            :key="match.id"
            class="flex items-center gap-3 rounded-xl bg-[#F9FAFB] p-3"
          >
            <!-- Icône victoire/défaite -->
            <div
              class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full"
              :class="match.result === 'win' ? 'bg-primary/10' : 'bg-destructive/10'"
            >
              <Trophy v-if="match.result === 'win'" class="h-4 w-4 text-primary" />
              <Frown v-else class="h-4 w-4 text-destructive" />
            </div>

            <!-- Avatar -->
            <div class="w-9 h-9 rounded-full flex-shrink-0 overflow-hidden">
              <img
                v-if="match.opponent.avatar"
                :src="match.opponent.avatar"
                :alt="match.opponent.name"
                class="w-full h-full object-cover"
              />
              <div
                v-else
                class="w-full h-full flex items-center justify-center text-white text-xs font-bold"
                :style="{ backgroundColor: getAvatarColor(match.opponent.name) }"
              >
                {{ getInitials(match.opponent.name) }}
              </div>
            </div>

            <!-- Infos -->
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-foreground truncate">{{ match.opponent.name }}</p>
              <p class="text-xs text-muted-foreground">{{ match.myScore }} – {{ match.opponentScore }}</p>
            </div>

            <!-- ELO + date -->
            <div class="shrink-0 text-right">
              <p class="text-sm font-bold" :class="match.eloChange >= 0 ? 'text-primary' : 'text-destructive'">
                {{ match.eloChange >= 0 ? '+' : '' }}{{ match.eloChange }}
              </p>
              <p class="text-xs text-muted-foreground">{{ match.date }}</p>
            </div>

          </div>

          <!-- Message si aucun résultat -->
          <p v-if="filteredMatches.length === 0" class="text-center text-sm py-6" style="color: #a8a29e;">
            Aucun match trouvé
          </p>

          </div>


        </div>
      </div>

    </div>
  </div>
  <BottomNavBar active="matchs" />
  </PlayerLayout>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
</style>  