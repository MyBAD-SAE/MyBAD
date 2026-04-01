<script setup>
import { Badge } from '@/Components/ui/badge/index.ts';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const hasActiveSession = computed(() => usePage().props.hasActiveSession);
</script>

<template>
    <Link
        v-if="hasActiveSession"
        :href="route('match.declare')"
        class="block relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#27BDAE] to-[#5DC9BC] p-6 text-white"
    >
        <Badge class="bg-white/20 text-white hover:bg-white/30 border-0 text-xs font-semibold uppercase tracking-wider">
            Suggestion
        </Badge>
        <h2 class="mt-4 text-2xl font-bold">Nouveau match</h2>
        <p class="mt-1 text-sm text-white/80">Lancer un nouveau match</p>
        <div class="absolute -bottom-6 -right-6 h-24 w-24 rounded-full bg-white/10" />
    </Link>

    <div v-else class="block relative overflow-hidden rounded-2xl bg-gradient-to-br from-gray-300 to-gray-400 p-6 text-white cursor-not-allowed">
        <Badge class="bg-white/20 text-white border-0 text-xs font-semibold uppercase tracking-wider">
            Aucune séance
        </Badge>
        <h2 class="mt-4 text-2xl font-bold">Nouveau match</h2>
        <p class="mt-1 text-sm text-white/80">Pas de séance active en ce moment</p>
        <div class="absolute -bottom-6 -right-6 h-24 w-24 rounded-full bg-white/10" />
    </div>
</template>
