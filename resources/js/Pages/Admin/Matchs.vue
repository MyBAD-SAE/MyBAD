<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Input } from '@/Components/ui/input';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import {
    ArrowLeft,
    Swords,
    Search,
    ChevronDown,
    Calendar,
    Pencil,
    Trash2,
} from 'lucide-vue-next';

const props = defineProps({
    sessions: { type: Array, default: () => [] },
    totalMatchCount: { type: Number, default: 0 },
    topMatchesPlayer: { type: String, default: null },
    topWinsPlayer: { type: String, default: null },
    classes: { type: Array, default: () => [] },
    selectedClassId: { type: Number, default: null },
});

const search = ref('');
const activeSession = ref('all');
const sortOpen = ref(false);
const sortBy = ref('recent');

const sortOptions = [
    { value: 'recent', label: 'Plus récent' },
    { value: 'oldest', label: 'Plus ancien' },
];

const allMatches = computed(() => {
    return props.sessions.flatMap(s => s.matches);
});

const filteredSessions = computed(() => {
    let sessions = props.sessions;

    if (activeSession.value !== 'all') {
        sessions = sessions.filter(s => s.id === activeSession.value);
    }

    if (sortBy.value === 'oldest') {
        sessions = [...sessions].reverse();
    }

    if (search.value.trim()) {
        const q = search.value.toLowerCase();
        sessions = sessions.map(s => ({
            ...s,
            matches: s.matches.filter(m =>
                m.player1.name.toLowerCase().includes(q) ||
                m.player2.name.toLowerCase().includes(q)
            ),
        })).filter(s => s.matches.length > 0);
    }

    return sessions;
});

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(p => p.charAt(0)).join('').toUpperCase();
};
</script>

<template>
    <Head title="Matchs - Admin" />

    <AdminLayout>
        <div class="p-8">
            <!-- Back link -->
            <Link :href="route('admin.dashboard')" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground transition-colors mb-6">
                <ArrowLeft class="h-4 w-4" />
                Retour au dashboard
            </Link>

            <!-- Header card -->
            <div class="rounded-2xl border border-border p-6 mb-6">
                <div class="flex items-center gap-4 mb-5">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100">
                        <Swords class="h-6 w-6 text-orange-500" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl font-bold text-foreground">Matchs</h1>
                            <span class="inline-flex h-6 min-w-6 items-center justify-center rounded-full bg-gray-100 px-2 text-xs font-semibold text-foreground">{{ totalMatchCount }}</span>
                        </div>
                        <p class="text-sm text-muted-foreground">Historique complet des matchs du groupe</p>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="rounded-xl border border-border py-4 text-center">
                        <p class="text-xl font-bold text-foreground">{{ totalMatchCount }}</p>
                        <p class="text-xs text-muted-foreground">Total matchs</p>
                    </div>
                    <div class="rounded-xl border border-border py-4 text-center">
                        <p class="text-xl font-bold text-foreground">{{ topMatchesPlayer ?? '-' }}</p>
                        <p class="text-xs text-muted-foreground">Joueur avec le plus de matchs</p>
                    </div>
                    <div class="rounded-xl border border-border py-4 text-center">
                        <p class="text-xl font-bold text-foreground">{{ topWinsPlayer ?? '-' }}</p>
                        <p class="text-xs text-muted-foreground">Joueur avec le plus de victoire</p>
                    </div>
                </div>
            </div>

            <!-- Session tabs (carousel) -->
            <div class="mb-4 -mr-8 overflow-x-auto" style="-ms-overflow-style: none; scrollbar-width: none;" ref="tabsScroll">
                <div class="flex items-center gap-3 pr-8">
                    <button
                        class="inline-flex shrink-0 items-center gap-2 rounded-2xl px-5 py-2.5 text-sm font-medium transition-colors cursor-pointer"
                        :class="activeSession === 'all' ? 'bg-gray-900 text-white' : 'border border-gray-200 bg-white text-muted-foreground hover:bg-gray-50'"
                        @click="activeSession = 'all'"
                    >
                        Tout
                        <span class="text-sm" :class="activeSession === 'all' ? 'text-white/60' : 'text-muted-foreground/60'">{{ totalMatchCount }}</span>
                    </button>
                    <button
                        v-for="session in sessions"
                        :key="session.id"
                        class="inline-flex shrink-0 items-center gap-2 rounded-2xl px-5 py-2.5 text-sm font-medium transition-colors cursor-pointer"
                        :class="activeSession === session.id ? 'bg-gray-900 text-white' : 'border border-gray-200 bg-white text-muted-foreground hover:bg-gray-50'"
                        @click="activeSession = session.id"
                    >
                        {{ session.label }}
                        <span class="text-sm" :class="activeSession === session.id ? 'text-white/60' : 'text-muted-foreground/60'">{{ session.matchCount }}</span>
                    </button>
                </div>
            </div>

            <!-- Search + Sort -->
            <div class="flex items-center gap-4 mb-4">
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                    <Input
                        v-model="search"
                        type="text"
                        placeholder="Rechercher un joueur..."
                        class="pl-9 w-full text-sm placeholder:text-gray-400 shadow-none focus-visible:ring-1 focus-visible:ring-primary"
                    />
                </div>
                <div class="relative">
                    <button
                        class="inline-flex h-9 items-center gap-2 rounded-md border border-input bg-white px-4 text-sm text-foreground transition-colors hover:bg-gray-50 cursor-pointer"
                        @click="sortOpen = !sortOpen"
                    >
                        {{ sortOptions.find(o => o.value === sortBy)?.label }}
                        <ChevronDown class="h-4 w-4 text-muted-foreground" />
                    </button>
                    <div v-if="sortOpen" class="absolute right-0 top-full z-50 mt-1 min-w-[150px] rounded-xl border border-border bg-white py-1 shadow-lg">
                        <button
                            v-for="opt in sortOptions"
                            :key="opt.value"
                            class="flex w-full px-4 py-2 text-left text-sm transition-colors hover:bg-gray-50 cursor-pointer"
                            :class="sortBy === opt.value && 'text-primary font-medium'"
                            @click="sortBy = opt.value; sortOpen = false"
                        >
                            {{ opt.label }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Match list grouped by session -->
            <div class="space-y-6">
                <div v-for="session in filteredSessions" :key="session.id" class="rounded-2xl border border-border overflow-hidden">
                    <!-- Session header -->
                    <div class="flex items-center justify-between bg-gray-50/70 px-5 py-4 border-b border-border">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-100">
                                <Calendar class="h-5 w-5 text-orange-500" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-foreground">{{ session.label }}</p>
                                <p class="text-xs text-muted-foreground">{{ session.date }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-muted-foreground border border-border rounded-full bg-white px-3 py-1">{{ session.matchCount }} matchs</span>
                    </div>

                    <!-- Matches -->
                    <div class="divide-y">
                        <div
                            v-for="match in session.matches"
                            :key="match.id"
                            class="flex items-center px-5 py-4"
                        >
                            <!-- Number -->
                            <span class="w-6 text-center text-sm text-muted-foreground shrink-0">{{ match.number }}</span>

                            <!-- Player 1 -->
                            <div class="flex items-center gap-3 flex-1 min-w-0 ml-3">
                                <Avatar class="h-10 w-10 shrink-0">
                                    <AvatarImage v-if="match.player1.avatar" :src="match.player1.avatar" />
                                    <AvatarFallback class="text-xs">{{ getInitials(match.player1.name) }}</AvatarFallback>
                                </Avatar>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-foreground truncate">{{ match.player1.name }}</p>
                                    <p v-if="match.player1.won" class="text-xs font-medium text-primary">Victoire</p>
                                    <p v-else class="text-xs font-medium text-destructive">Défaite</p>
                                </div>
                            </div>

                            <!-- Score -->
                            <div class="flex items-center gap-2 shrink-0 mx-4">
                                <span
                                    class="inline-flex h-9 w-10 items-center justify-center rounded-lg text-sm font-bold"
                                    :class="match.player1.won ? 'bg-primary/10 text-primary' : 'bg-gray-100 text-foreground'"
                                >
                                    {{ match.player1.score }}
                                </span>
                                <span class="text-xs text-muted-foreground">vs</span>
                                <span
                                    class="inline-flex h-9 w-10 items-center justify-center rounded-lg text-sm font-bold"
                                    :class="match.player2.won ? 'bg-primary/10 text-primary' : 'bg-gray-100 text-foreground'"
                                >
                                    {{ match.player2.score }}
                                </span>
                            </div>

                            <!-- Player 2 -->
                            <div class="flex items-center gap-3 flex-1 min-w-0 justify-end">
                                <div class="min-w-0 text-right">
                                    <p class="text-sm font-medium text-foreground truncate">{{ match.player2.name }}</p>
                                    <p v-if="match.player2.won" class="text-xs font-medium text-primary">Victoire</p>
                                    <p v-else class="text-xs font-medium text-destructive">Défaite</p>
                                </div>
                                <Avatar class="h-10 w-10 shrink-0">
                                    <AvatarImage v-if="match.player2.avatar" :src="match.player2.avatar" />
                                    <AvatarFallback class="text-xs">{{ getInitials(match.player2.name) }}</AvatarFallback>
                                </Avatar>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2 shrink-0 ml-4">
                                <button class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-lg transition-colors hover:bg-gray-100">
                                    <Pencil class="h-4 w-4 text-muted-foreground" />
                                </button>
                                <button class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-lg transition-colors hover:bg-red-50">
                                    <Trash2 class="h-4 w-4 text-destructive" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty state -->
                <div v-if="filteredSessions.length === 0" class="py-12 text-center">
                    <p class="text-sm text-muted-foreground">Aucun match trouvé.</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
