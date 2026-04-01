<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button/index.ts';
import { Input } from '@/Components/ui/input/index.ts';
import { Label } from '@/Components/ui/label/index.ts';
import { Mail, Lock, Eye, EyeOff, ArrowRight, User, LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showConfirmation = ref(false);

const passwordStrength = computed(() => {
    const p = form.password;
    if (!p) return 0;
    let score = 0;
    if (p.length >= 8) score++;
    if (p.length >= 12) score++;
    if (/[A-Z]/.test(p)) score++;
    if (/[0-9]/.test(p)) score++;
    if (/[^A-Za-z0-9]/.test(p)) score++;
    return score;
});

const strengthLabel = computed(() => {
    const labels = ['', 'Très faible', 'Faible', 'Moyen', 'Fort', 'Très fort'];
    return labels[passwordStrength.value] || '';
});

const strengthColor = computed(() => {
    const colors = ['', '#D32F2F', '#F59E0B', '#F59E0B', '#009966', '#009966'];
    return colors[passwordStrength.value] || '';
});

const submit = () => {
    form.post(route('admin.register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <form class="space-y-6" @submit.prevent="submit">
        <div>
            <h2 class="text-xl font-bold">Créer un compte</h2>
            <p class="mt-1 text-sm text-muted-foreground">Créez votre espace administrateur</p>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <Label for="admin-register-firstname">Prénom</Label>
                    <div class="relative">
                        <User class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            id="admin-register-firstname"
                            v-model="form.first_name"
                            placeholder="Julien"
                            class="pl-10"
                        />
                    </div>
                    <p v-if="form.errors.first_name" class="text-sm text-destructive">{{ form.errors.first_name }}</p>
                </div>
                <div class="space-y-2">
                    <Label for="admin-register-lastname">Nom</Label>
                    <Input
                        id="admin-register-lastname"
                        v-model="form.last_name"
                        placeholder="Dupont"
                    />
                    <p v-if="form.errors.last_name" class="text-sm text-destructive">{{ form.errors.last_name }}</p>
                </div>
            </div>

            <div class="space-y-2">
                <Label for="admin-register-email">Email</Label>
                <div class="relative">
                    <Mail class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        id="admin-register-email"
                        v-model="form.email"
                        type="email"
                        placeholder="nom@exemple.com"
                        class="pl-10"
                    />
                </div>
                <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
            </div>

            <div class="space-y-2">
                <Label for="admin-register-password">Mot de passe</Label>
                <div class="relative">
                    <Lock class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        id="admin-register-password"
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        placeholder="Min. 8 caractères"
                        class="pl-10 pr-10"
                    />
                    <button
                        type="button"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                        @click="showPassword = !showPassword"
                    >
                        <EyeOff v-if="showPassword" class="h-4 w-4" />
                        <Eye v-else class="h-4 w-4" />
                    </button>
                </div>
                <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>

                <div v-if="form.password" class="space-y-1">
                    <div class="flex gap-1">
                        <div
                            v-for="i in 5"
                            :key="i"
                            class="h-1.5 flex-1 rounded-full transition-colors"
                            :style="{ backgroundColor: i <= passwordStrength ? strengthColor : '#e5e7eb' }"
                        />
                    </div>
                    <p class="text-xs font-medium" :style="{ color: strengthColor }">{{ strengthLabel }}</p>
                </div>
            </div>

            <div class="space-y-2">
                <Label for="admin-register-confirm">Confirmer</Label>
                <div class="relative">
                    <Lock class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        id="admin-register-confirm"
                        v-model="form.password_confirmation"
                        :type="showConfirmation ? 'text' : 'password'"
                        placeholder="Retapez le mot de passe"
                        class="pl-10 pr-10"
                    />
                    <button
                        type="button"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                        @click="showConfirmation = !showConfirmation"
                    >
                        <EyeOff v-if="showConfirmation" class="h-4 w-4" />
                        <Eye v-else class="h-4 w-4" />
                    </button>
                </div>
                <p v-if="form.errors.password_confirmation" class="text-sm text-destructive">{{ form.errors.password_confirmation }}</p>
            </div>
        </div>

        <Button type="submit" class="w-full" size="lg" :disabled="form.processing">
            <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
            Créer mon compte
            <ArrowRight v-if="!form.processing" class="ml-2 h-4 w-4" />
        </Button>
    </form>
</template>
