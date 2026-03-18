<script setup>
import { ref, reactive } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Checkbox } from '@/Components/ui/checkbox';
import GoogleButton from '@/Components/GoogleButton.vue';
import SocialDivider from '@/Components/SocialDivider.vue';
import ConditionsFooter from '@/Components/ConditionsFooter.vue';
import { Mail, Lock, Eye, EyeOff, ArrowRight, LoaderCircle } from 'lucide-vue-next';

const props = defineProps({
    submitRoute: { type: String, default: null },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);
const touched = reactive({ email: false, password: false });

const isValidEmail = (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
const emailError = () => touched.email && !form.email;
const emailFormatError = () => touched.email && form.email && !isValidEmail(form.email);
const passwordError = () => touched.password && !form.password;

const submit = () => {
    touched.email = true;
    touched.password = true;

    if (!form.email || !isValidEmail(form.email) || !form.password) return;

    form.post(route(props.submitRoute ?? 'player.login.submit'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="lg:hidden">
            <h2 class="text-xl font-bold">Connexion</h2>
            <p class="mt-1 text-sm text-muted-foreground">Connectez-vous pour retrouver votre dashboard</p>
        </div>

        <!-- Erreur serveur -->
        <div v-if="form.errors.email" class="rounded-md bg-destructive/10 p-3 text-sm text-destructive">
            {{ form.errors.email }}
        </div>

        <div class="space-y-4">
            <div class="space-y-2">
                <Label for="login-email">Adresse email</Label>
                <div class="relative">
                    <Mail class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        id="login-email"
                        v-model="form.email"
                        type="email"
                        placeholder="nom@exemple.com"
                        class="pl-10"
                        :aria-invalid="emailError() || emailFormatError()"
                        @blur="touched.email = true"
                    />
                </div>
                <p v-if="emailError()" class="text-sm text-destructive">L'email est requis.</p>
                <p v-else-if="emailFormatError()" class="text-sm text-destructive">Format d'email invalide.</p>
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <Label for="login-password">Mot de passe</Label>
                    <Link :href="route('password.request')" class="text-sm font-medium text-primary hover:underline">
                        Oublié ?
                    </Link>
                </div>
                <div class="relative">
                    <Lock class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        id="login-password"
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        placeholder="Votre mot de passe"
                        class="pl-10 pr-10"
                        :aria-invalid="passwordError()"
                        @blur="touched.password = true"
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
                <p v-if="passwordError()" class="text-sm text-destructive">Le mot de passe est requis.</p>
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="remember"
                    :checked="form.remember"
                    @update:checked="form.remember = $event"
                />
                <Label for="remember" class="text-sm font-normal">Se souvenir de moi</Label>
            </div>
        </div>

        <Button type="submit" class="w-full" size="lg" :disabled="form.processing">
            <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
            Se connecter
            <ArrowRight v-if="!form.processing" class="ml-2 h-4 w-4" />
        </Button>

        <SocialDivider />
        <GoogleButton />
        <ConditionsFooter />
    </form>
</template>
