<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
</script>

<template>
    <Head title="Regles et defis - Admin" />

    <AdminLayout>
        <div class="p-8">
            <h1 class="text-2xl font-bold text-foreground">Regles et defis</h1>
            <p class="mt-2 text-sm text-muted-foreground">Configurer les regles et creer des defis.</p>
        </div>
    </AdminLayout>
</template>
