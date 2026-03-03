<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="MyBAD - Badminton" />

    <div class="min-h-screen bg-white">
        <!-- Nav -->
        <nav class="flex items-center justify-between px-6 py-4 max-w-7xl mx-auto">
            <ApplicationLogo class="text-primary-600" />
            <div v-if="canLogin" class="flex items-center gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700 transition"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="text-sm font-medium text-gray-700 hover:text-primary-600 transition"
                    >
                        Connexion
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700 transition"
                    >
                        Inscription
                    </Link>
                </template>
            </div>
        </nav>

        <!-- Hero -->
        <div class="relative overflow-hidden">
            <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:py-40 text-center">
                <!-- Shuttlecock decorative -->
                <div class="flex justify-center mb-8">
                    <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-primary-500 opacity-90">
                        <ellipse cx="32" cy="48" rx="8" ry="6" fill="currentColor" />
                        <path d="M28 46 L20 16 Q32 20 32 20 L24 46" fill="currentColor" opacity="0.7" />
                        <path d="M32 44 L28 12 Q32 8 36 12 L32 44" fill="currentColor" opacity="0.8" />
                        <path d="M36 46 L44 16 Q32 20 32 20 L40 46" fill="currentColor" opacity="0.7" />
                    </svg>
                </div>

                <h1 class="text-5xl font-extrabold tracking-tight text-gray-900 sm:text-7xl">
                    My<span class="text-primary-600">BAD</span>
                </h1>
                <p class="mt-4 text-xl text-gray-500 max-w-2xl mx-auto">
                    L'application de badminton pour votre classe. Defiez vos camarades, enregistrez vos scores et grimpez le classement ELO !
                </p>

                <div class="mt-10 flex items-center justify-center gap-4">
                    <Link
                        v-if="canRegister && !$page.props.auth.user"
                        :href="route('register')"
                        class="rounded-xl bg-primary-600 px-8 py-3 text-lg font-semibold text-white shadow-lg hover:bg-primary-700 transition"
                    >
                        Commencer
                    </Link>
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-xl bg-primary-600 px-8 py-3 text-lg font-semibold text-white shadow-lg hover:bg-primary-700 transition"
                    >
                        Mon Dashboard
                    </Link>
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="bg-gray-50 py-20">
            <div class="mx-auto max-w-7xl px-6">
                <h2 class="text-center text-3xl font-bold text-gray-900 mb-12">Comment ca marche ?</h2>
                <div class="grid gap-8 md:grid-cols-3">
                    <div class="rounded-xl bg-white p-8 shadow-sm border border-gray-100 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-primary-100 text-primary-600 text-2xl font-bold">1</div>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">Creez votre compte</h3>
                        <p class="mt-2 text-sm text-gray-500">Inscrivez-vous et rejoignez la communaute de joueurs.</p>
                    </div>
                    <div class="rounded-xl bg-white p-8 shadow-sm border border-gray-100 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-primary-100 text-primary-600 text-2xl font-bold">2</div>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">Defiez un joueur</h3>
                        <p class="mt-2 text-sm text-gray-500">Envoyez un defi a un camarade et jouez votre match.</p>
                    </div>
                    <div class="rounded-xl bg-white p-8 shadow-sm border border-gray-100 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-primary-100 text-primary-600 text-2xl font-bold">3</div>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">Grimpez le classement</h3>
                        <p class="mt-2 text-sm text-gray-500">Gagnez des points ELO et atteignez le sommet du classement !</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="py-8 text-center text-sm text-gray-400">
            MyBAD &copy; {{ new Date().getFullYear() }} - Application de badminton
        </footer>
    </div>
</template>
