<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
</script>

<template>
    <Head title="Matchs - Admin" />

    <AdminLayout>
        <div class="p-8">
            <h1 class="text-2xl font-bold text-foreground">Matchs</h1>
            <p class="mt-2 text-sm text-muted-foreground">Historique et validation des matchs.</p>
        </div>
    </AdminLayout>
</template>
