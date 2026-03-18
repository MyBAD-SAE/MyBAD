<script setup>
import { Monitor } from 'lucide-vue-next';
</script>

<template>
    <!-- Mobile : contenu normal -->
    <div class="min-h-screen bg-white sm:hidden">
        <slot />
    </div>

    <!-- Desktop / Tablette : message de blocage -->
    <div class="hidden min-h-screen sm:flex flex-col items-center justify-center bg-background px-8 text-center">
        <img src="/images/logo_mybad.png" alt="MyBAD" class="h-16 w-16 rounded-xl" />
        <h1 class="mt-6 text-xl font-bold text-foreground">Application mobile uniquement</h1>
        <p class="mt-2 max-w-sm text-sm text-muted-foreground">
            L'interface joueur de MyBAD est conçue pour les smartphones. Veuillez vous connecter depuis votre téléphone pour accéder à l'application.
        </p>
        <div class="mt-6 flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-3">
            <Monitor class="h-5 w-5 text-muted-foreground" />
            <span class="text-sm text-muted-foreground">Desktop et tablette non supportés</span>
        </div>
    </div>
</template>
