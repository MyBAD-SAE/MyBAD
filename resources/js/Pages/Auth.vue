<script setup>
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import PlayerLayout from '@/Layouts/PlayerLayout.vue';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/Components/ui/tabs';
import LoginForm from '@/Components/LoginForm.vue';
import RegisterForm from '@/Components/RegisterForm.vue';

const activeTab = ref('login');
</script>

<template>
    <Head title="Connexion" />

    <PlayerLayout>
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
                    <h1 class="text-2xl font-bold text-white">Suivez vos performances</h1>
                    <p class="mt-1 text-sm text-white/80">ELO, stats, classement. Tout pour progresser.</p>
                </div>
            </div>
        </div>

        <div class="px-6 py-6">
            <Tabs v-model="activeTab" class="w-full">
                <div class="flex justify-center">
                    <TabsList class="grid w-full grid-cols-2">
                        <TabsTrigger value="login">Connexion</TabsTrigger>
                        <TabsTrigger value="register">Inscription</TabsTrigger>
                    </TabsList>
                </div>

                <TabsContent value="login" class="mt-6">
                    <LoginForm />
                </TabsContent>

                <TabsContent value="register" class="mt-6">
                    <RegisterForm />
                </TabsContent>
            </Tabs>
        </div>
    </PlayerLayout>
</template>
