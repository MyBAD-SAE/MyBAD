<script setup>
import { ref, reactive, computed } from 'vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Checkbox } from '@/Components/ui/checkbox';
import GoogleButton from '@/Components/GoogleButton.vue';
import SocialDivider from '@/Components/SocialDivider.vue';
import { Link } from '@inertiajs/vue3';
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

function stripSpaces(field) {
    form.value[field] = form.value[field].replace(/\s/g, '');
}

const passwordStrength = computed(() => {
    const p = form.value.password;
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
                        @input="stripSpaces('password')"
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
                <div v-if="form.password" class="space-y-1.5">
                    <div class="flex gap-1">
                        <div
                            v-for="i in 5"
                            :key="i"
                            class="h-1 flex-1 rounded-full transition-colors"
                            :style="{ backgroundColor: i <= passwordStrength ? strengthColor : '#e7e5e4' }"
                        />
                    </div>
                    <p class="text-xs" :style="{ color: strengthColor }">{{ strengthLabel }}</p>
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
                        @input="stripSpaces('passwordConfirmation')"
                        @paste.prevent
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
                <Label for="terms" class="text-xs font-normal leading-snug">
                    J'accepte les <Link :href="route('terms')" class="inline font-medium text-primary hover:underline">CGU</Link> et la <Link :href="route('privacy')" class="inline font-medium text-primary hover:underline">politique de confidentialité</Link>
                </Label>
            </div>
        </div>

        <Button class="w-full" size="lg" :disabled="!form.acceptTerms">
            Créer mon compte
            <ArrowRight class="ml-2 h-4 w-4" />
        </Button>

        <SocialDivider />
        <GoogleButton />
    </div>
</template>
