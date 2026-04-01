<script setup>
import { Head } from '@inertiajs/vue3';
import PlayerLayout from '@/Layouts/PlayerLayout.vue';
import BottomNavBar from '@/Components/player/BottomNavBar.vue';
</script>

<template>
    <Head title="Matchs" />

    <PlayerLayout>
        <div class="pb-20">
            <div class="p-5">
                <h1 class="text-2xl font-bold text-foreground">Matchs</h1>
            </div>
        </div>

        <BottomNavBar active="matchs" />
    </PlayerLayout>
</template>
