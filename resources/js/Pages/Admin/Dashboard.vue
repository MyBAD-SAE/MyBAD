<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;
</script>

<template>
    <Head title="Dashboard Admin" />

    <div class="flex min-h-screen items-center justify-center bg-background">
        <div class="w-full max-w-md space-y-6 p-8 text-center">
            <h1 class="text-3xl font-bold">Bienvenue, {{ user.first_name }}</h1>
            <p class="text-muted-foreground">Espace administrateur</p>

            <div class="rounded-lg border p-4 text-left text-sm space-y-1">
                <p><span class="font-medium">Nom :</span> {{ user.last_name }}</p>
                <p><span class="font-medium">Prénom :</span> {{ user.first_name }}</p>
                <p><span class="font-medium">Email :</span> {{ user.email }}</p>
            </div>

            <Link
                :href="route('admin.logout')"
                method="post"
                as="button"
                class="inline-flex items-center rounded-md bg-destructive px-4 py-2 text-sm font-medium text-destructive-foreground hover:bg-destructive/90"
            >
                Se déconnecter
            </Link>
        </div>
    </div>
</template>
