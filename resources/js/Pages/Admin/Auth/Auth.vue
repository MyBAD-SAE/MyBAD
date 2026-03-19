<script setup>
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/Components/ui/tabs';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/card';
import LoginForm from '@/Components/LoginForm.vue';
import RegisterForm from '@/Components/RegisterForm.vue';

const activeTab = ref('login');
</script>

<template>

    <Head title="Connexion Admin" />

    <AuthLayout title="Espace administrateur" subtitle="Gérez vos classes et vos joueurs."
        desktopTitle="Espace administrateur"
        desktopSubtitle="Gérez vos classes, vos sessions et suivez la progression de vos joueurs.">
        <Tabs v-model="activeTab" class="w-full">
            <div class="flex justify-center">
                <TabsList class="grid w-full grid-cols-2 lg:w-64">
                    <TabsTrigger value="login">Connexion</TabsTrigger>
                    <TabsTrigger value="register">Inscription</TabsTrigger>
                </TabsList>
            </div>

            <TabsContent value="login" class="mt-6 lg:mt-8">
                <!-- Mobile: direct form -->
                <div class="lg:hidden">
                    <LoginForm submitRoute="admin.login.submit" />
                </div>
                <!-- Desktop: wrapped in Card -->
                <div class="hidden lg:block">
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-xl">Connexion Admin</CardTitle>
                            <CardDescription>Connectez-vous pour accéder à votre espace de gestion</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <LoginForm submitRoute="admin.login.submit" />
                        </CardContent>
                    </Card>
                </div>
            </TabsContent>

            <TabsContent value="register" class="mt-6 lg:mt-8">
                <!-- Mobile: direct form -->
                <div class="lg:hidden">
                    <RegisterForm />
                </div>
                <!-- Desktop: wrapped in Card -->
                <div class="hidden lg:block">
                    <Card>
                        <CardContent class="pt-6">
                            <RegisterForm />
                        </CardContent>
                    </Card>
                </div>
            </TabsContent>
        </Tabs>
    </AuthLayout>
</template>
