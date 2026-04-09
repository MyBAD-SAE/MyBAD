<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Monitor, Shield } from 'lucide-vue-next';
import CreatePinModal from '@/Components/player/CreatePinModal.vue';

const showPinModal = computed(() => usePage().props.auth.hasPin === false);
</script>

<template>
    <!-- Mobile : contenu normal -->
    <div class="min-h-screen bg-white sm:hidden">
        <slot />
        <CreatePinModal :model-value="showPinModal" />
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
        <Link
            :href="route('admin.login')"
            class="mt-6 inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-primary/90"
        >
            <Shield class="h-4 w-4" />
            Accéder à l'espace administrateur
        </Link>
    </div>
</template>
