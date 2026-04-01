<script setup>
defineProps({
    title: { type: String, default: 'Suivez vos performances' },
    subtitle: { type: String, default: 'ELO, stats, classement. Tout pour progresser.' },
    desktopTitle: { type: String, default: "Suivez vos performances\nau badminton" },
    desktopSubtitle: { type: String, default: "Classement ELO, statistiques de matchs, suivi de progression. Tout ce qu'il faut pour devenir le meilleur joueur de votre club." },
});
</script>

<template>
    <!-- Mobile layout -->
    <div class="min-h-screen bg-background lg:hidden">
        <div class="relative h-56 overflow-hidden bg-gray-900">
            <img
                src="/images/hero-badminton.png"
                alt="Terrain de badminton"
                class="absolute inset-0 h-full w-full object-cover"
            />
            <div class="absolute inset-0 bg-[#27BDAE]/70 mix-blend-multiply" />
            <div class="relative z-10 flex h-full flex-col justify-between p-6">
                <div class="flex items-center gap-2">
                    <img src="/images/logo_mybad.png" alt="MyBAD" class="h-8 w-8 rounded-md" />
                    <span class="text-lg font-bold text-white">MyBAD</span>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white">{{ title }}</h1>
                    <p class="mt-1 text-sm text-white/80">{{ subtitle }}</p>
                </div>
            </div>
        </div>

        <div class="px-6 py-6">
            <slot />
        </div>
    </div>

    <!-- Desktop layout -->
    <div class="hidden min-h-screen lg:flex">
        <div class="relative w-2/5 bg-gray-900">
            <img
                src="/images/hero-badminton.png"
                alt="Terrain de badminton"
                class="absolute inset-0 h-full w-full object-cover"
            />
            <div class="absolute inset-0 bg-[#27BDAE]/70 mix-blend-multiply" />
            <div class="relative z-10 flex h-full flex-col justify-between p-10">
                <div class="flex items-center gap-2">
                    <img src="/images/logo_mybad.png" alt="MyBAD" class="h-10 w-10 rounded-md" />
                    <span class="text-xl font-bold text-white">MyBAD</span>
                </div>
                <div class="mb-16">
                    <h1 class="whitespace-pre-line text-4xl font-bold leading-tight text-white">{{ desktopTitle }}</h1>
                    <p class="mt-3 max-w-sm text-base text-white/80">{{ desktopSubtitle }}</p>
                </div>
            </div>
        </div>

        <div class="flex w-3/5 flex-col items-center justify-start overflow-y-auto bg-background px-8 py-10">
            <div class="w-full max-w-md">
                <slot />
            </div>
        </div>
    </div>
</template>
