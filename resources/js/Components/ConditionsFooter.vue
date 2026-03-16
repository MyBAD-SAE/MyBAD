<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <p class="text-center text-xs text-muted-foreground">
        En continuant, vous acceptez nos
        <Link :href="route('terms')" class="underline hover:text-foreground">conditions d'utilisation</Link>.
    </p>
</template>
