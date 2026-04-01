<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <p class="mt-2 text-center text-xs text-muted-foreground">
        En continuant, vous acceptez nos
        <Link :href="route('player.terms')" class="underline hover:text-foreground">conditions d'utilisation</Link>.
    </p>
</template>
