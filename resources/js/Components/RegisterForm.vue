<script setup>
import { ref, reactive } from 'vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Checkbox } from '@/Components/ui/checkbox';
import GoogleButton from '@/Components/GoogleButton.vue';
import SocialDivider from '@/Components/SocialDivider.vue';
import { Mail, Lock, Eye, EyeOff, ArrowRight, User } from 'lucide-vue-next';

const form = ref({
    firstName: '',
    lastName: '',
    email: '',
    password: '',
    passwordConfirmation: '',
    acceptTerms: false,
});

const showPassword = ref(false);
const showConfirmation = ref(false);

const touched = reactive({
    firstName: false,
    lastName: false,
    email: false,
    password: false,
    confirmation: false,
});

const isValidEmail = (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
const empty = (field, value) => touched[field] && !value;
const emailFormatError = () => touched.email && form.value.email && !isValidEmail(form.value.email);
const passwordMinError = () => touched.password && form.value.password && form.value.password.length < 8;
const confirmError = () => touched.confirmation && form.value.passwordConfirmation && form.value.passwordConfirmation !== form.value.password;
</script>

<template>
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-bold">Créer un compte</h2>
            <p class="mt-1 text-sm text-muted-foreground">Rejoignez la communauté MyBAD en quelques secondes</p>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <Label for="register-firstname">Prénom</Label>
                    <div class="relative">
                        <User class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            id="register-firstname"
                            v-model="form.firstName"
                            placeholder="Lucas"
                            class="pl-10"
                            :aria-invalid="empty('firstName', form.firstName)"
                            @blur="touched.firstName = true"
                        />
                    </div>
                    <p v-if="empty('firstName', form.firstName)" class="text-sm text-destructive">Le prénom est requis.</p>
                </div>
                <div class="space-y-2">
                    <Label for="register-lastname">Nom</Label>
                    <Input
                        id="register-lastname"
                        v-model="form.lastName"
                        placeholder="Torres"
                        :aria-invalid="empty('lastName', form.lastName)"
                        @blur="touched.lastName = true"
                    />
                    <p v-if="empty('lastName', form.lastName)" class="text-sm text-destructive">Le nom est requis.</p>
                </div>
            </div>

            <div class="space-y-2">
                <Label for="register-email">Email</Label>
                <div class="relative">
                    <Mail class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        id="register-email"
                        v-model="form.email"
                        type="email"
                        placeholder="nom@exemple.com"
                        class="pl-10"
                        :aria-invalid="emailFormatError() || empty('email', form.email)"
                        @blur="touched.email = true"
                    />
                </div>
                <p v-if="empty('email', form.email)" class="text-sm text-destructive">L'email est requis.</p>
                <p v-else-if="emailFormatError()" class="text-sm text-destructive">Format d'email invalide.</p>
            </div>

            <div class="space-y-2">
                <Label for="register-password">Mot de passe</Label>
                <div class="relative">
                    <Lock class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        id="register-password"
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        placeholder="Min. 8 caractères"
                        class="pl-10 pr-10"
                        :aria-invalid="passwordMinError()"
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
                <p v-if="passwordMinError()" class="text-sm text-destructive">Le mot de passe doit contenir au moins 8 caractères.</p>
            </div>

            <div class="space-y-2">
                <Label for="register-confirm">Confirmer</Label>
                <div class="relative">
                    <Lock class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        id="register-confirm"
                        v-model="form.passwordConfirmation"
                        :type="showConfirmation ? 'text' : 'password'"
                        placeholder="Retapez le mot de passe"
                        class="pl-10 pr-10"
                        :aria-invalid="confirmError()"
                        @blur="touched.confirmation = true"
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
                <p v-if="confirmError()" class="text-sm text-destructive">Les mots de passe ne correspondent pas.</p>
            </div>

            <div class="flex items-start gap-2">
                <Checkbox
                    id="terms"
                    :checked="form.acceptTerms"
                    @update:checked="form.acceptTerms = $event"
                    class="mt-0.5"
                />
                <Label for="terms" class="text-sm font-normal leading-snug">
                    J'accepte les
                    <button class="font-medium text-primary hover:underline">CGU</button>
                    et la
                    <button class="font-medium text-primary hover:underline">politique de confidentialité</button>
                </Label>
            </div>
        </div>

        <Button class="w-full" size="lg">
            Créer mon compte
            <ArrowRight class="ml-2 h-4 w-4" />
        </Button>

        <SocialDivider />
        <GoogleButton />
    </div>
</template>
