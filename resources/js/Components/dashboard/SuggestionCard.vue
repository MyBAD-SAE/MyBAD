<script setup>
import { Badge } from '@/Components/ui/badge';
</script>

<template>
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#3DD4C4] to-[#7DE5DA] p-6 text-white">
        <Badge class="bg-white/20 text-white hover:bg-white/30 border-0 text-xs font-semibold uppercase tracking-wider">
            Suggestion
        </Badge>
        <h2 class="mt-4 text-2xl font-bold">Nouveau match</h2>
        <p class="mt-1 text-sm text-white/80">Lancer un nouveau match</p>
        <!-- Decorative circle -->
        <div class="absolute -bottom-6 -right-6 h-24 w-24 rounded-full bg-white/10" />
    </div>
</template>
