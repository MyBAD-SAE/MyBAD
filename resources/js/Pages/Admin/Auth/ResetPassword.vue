<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Lock, Eye, EyeOff, ArrowRight, LoaderCircle } from 'lucide-vue-next';

const props = defineProps({
    token: String,
    email: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showConfirm = ref(false);

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

function handleSubmit() {
    form.post(route('admin.password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <Head title="Nouveau mot de passe" />

    <AuthLayout
        title="Nouveau mot de passe"
        subtitle="Choisissez un nouveau mot de passe."
        image="/images/muktasim-azlan-tkVwhG6yqKo-unsplash.jpg"
    >
        <div class="mt-6 space-y-6">
            <div>
                <h2 class="text-xl font-bold lg:hidden">Nouveau mot de passe</h2>
                <p class="mt-1 text-sm text-muted-foreground">
                    Entrez votre nouveau mot de passe ci-dessous.
                </p>
            </div>

            <div v-if="form.errors.email" class="rounded-md bg-destructive/10 p-3 text-sm text-destructive">
                {{ form.errors.email }}
            </div>

            <div class="space-y-4">
                <div class="space-y-2">
                    <Label for="reset-password">Nouveau mot de passe</Label>
                    <div class="relative">
                        <Lock class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            id="reset-password"
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="Votre nouveau mot de passe"
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
                    <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
                </div>

                <div class="space-y-2">
                    <Label for="reset-password-confirm">Confirmer le mot de passe</Label>
                    <div class="relative">
                        <Lock class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            id="reset-password-confirm"
                            v-model="form.password_confirmation"
                            :type="showConfirm ? 'text' : 'password'"
                            placeholder="Confirmez le mot de passe"
                            class="pl-10 pr-10"
                        />
                        <button
                            type="button"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                            @click="showConfirm = !showConfirm"
                        >
                            <EyeOff v-if="showConfirm" class="h-4 w-4" />
                            <Eye v-else class="h-4 w-4" />
                        </button>
                    </div>
                    <p v-if="form.errors.password_confirmation" class="text-sm text-destructive">{{ form.errors.password_confirmation }}</p>
                </div>
            </div>

            <Button class="w-full" size="lg" :disabled="form.processing" @click="handleSubmit">
                <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                Réinitialiser le mot de passe
                <ArrowRight v-if="!form.processing" class="ml-2 h-4 w-4" />
            </Button>
        </div>
    </AuthLayout>
</template>
