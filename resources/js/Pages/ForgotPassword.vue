<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Card, CardContent } from '@/Components/ui/card';
import ConditionsFooter from '@/Components/ConditionsFooter.vue';
import { Mail, ArrowLeft, ArrowRight, CheckCircle, RefreshCw } from 'lucide-vue-next';

const email = ref('');
const emailSent = ref(false);
const emailTouched = ref(false);

const isValidEmail = (val) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
const emailEmpty = () => emailTouched.value && !email.value;
const emailInvalid = () => emailTouched.value && email.value && !isValidEmail(email.value);

function handleSubmit() {
    emailTouched.value = true;
    if (email.value && isValidEmail(email.value)) {
        emailSent.value = true;
    }
}

function maskedEmail(addr) {
    if (!addr) return '';
    const [local, domain] = addr.split('@');
    if (!domain) return addr;
    const visible = local.slice(0, 2);
    return `${visible}${'•'.repeat(Math.max(local.length - 2, 4))}@${domain}`;
}
</script>

<template>
    <Head title="Mot de passe oublié" />

    <AuthLayout
        title="Réinitialiser votre mot de passe"
        subtitle="On vous envoie un lien par email."
    >
        <Link :href="route('auth')" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground">
            <ArrowLeft class="h-4 w-4" />
            Retour à la connexion
        </Link>

        <!-- Form state -->
        <template v-if="!emailSent">
            <!-- Mobile -->
            <div class="mt-6 space-y-6 lg:hidden">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <Mail class="h-5 w-5 text-primary" />
                </div>

                <div>
                    <h2 class="text-xl font-bold">Mot de passe oublié ?</h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="forgot-email">Adresse email</Label>
                    <div class="relative">
                        <Mail class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            id="forgot-email"
                            v-model="email"
                            type="email"
                            placeholder="nom@exemple.com"
                            class="pl-10"
                            :aria-invalid="emailEmpty() || emailInvalid()"
                            @blur="emailTouched = true"
                        />
                    </div>
                    <p v-if="emailEmpty()" class="text-sm text-destructive">L'email est requis.</p>
                    <p v-else-if="emailInvalid()" class="text-sm text-destructive">Format d'email invalide.</p>
                </div>

                <Button class="w-full" size="lg" @click="handleSubmit">
                    Envoyer le lien
                    <ArrowRight class="ml-2 h-4 w-4" />
                </Button>

                <div class="rounded-lg border border-border px-4 py-3 text-sm text-muted-foreground">
                    Le lien de réinitialisation expirera après <strong class="font-semibold text-foreground">15 minutes</strong>. Pensez aussi à vérifier vos spams.
                </div>

                <ConditionsFooter />
            </div>

            <!-- Desktop -->
            <Card class="mt-6 hidden lg:block">
                <CardContent class="space-y-6 pt-6">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                        <Mail class="h-5 w-5 text-primary" />
                    </div>

                    <div>
                        <h2 class="text-xl font-bold">Mot de passe oublié ?</h2>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="forgot-email-desktop">Adresse email</Label>
                        <div class="relative">
                            <Mail class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                id="forgot-email-desktop"
                                v-model="email"
                                type="email"
                                placeholder="nom@exemple.com"
                                class="pl-10"
                                :aria-invalid="emailEmpty() || emailInvalid()"
                                @blur="emailTouched = true"
                            />
                        </div>
                        <p v-if="emailEmpty()" class="text-sm text-destructive">L'email est requis.</p>
                        <p v-else-if="emailInvalid()" class="text-sm text-destructive">Format d'email invalide.</p>
                    </div>

                    <Button class="w-full" size="lg" @click="handleSubmit">
                        Envoyer le lien
                        <ArrowRight class="ml-2 h-4 w-4" />
                    </Button>

                    <div class="rounded-lg border border-border px-4 py-3 text-sm text-muted-foreground">
                        Le lien de réinitialisation expirera après <strong class="font-semibold text-foreground">15 minutes</strong>. Pensez aussi à vérifier vos spams.
                    </div>
                </CardContent>
            </Card>

            <div class="mt-4 hidden lg:block">
                <ConditionsFooter />
            </div>
        </template>

        <!-- Email sent state -->
        <template v-else>
            <!-- Mobile -->
            <div class="mt-6 space-y-6 text-center lg:hidden">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                    <CheckCircle class="h-6 w-6 text-primary" />
                </div>

                <div>
                    <h2 class="text-xl font-bold">Email envoyé !</h2>
                    <p class="mt-1 text-sm text-muted-foreground">Un lien de réinitialisation a été envoyé à</p>
                </div>

                <div class="inline-flex items-center gap-2 rounded-lg border px-4 py-2">
                    <Mail class="h-4 w-4 text-muted-foreground" />
                    <span class="text-sm font-medium">{{ maskedEmail(email) }}</span>
                </div>

                <p class="text-sm text-muted-foreground">
                    Cliquez sur le lien dans l'email pour créer un nouveau mot de passe. Le lien expire dans 15 minutes.
                </p>

                <Button class="w-full" size="lg" @click="emailSent = false">
                    Retour à la connexion
                    <ArrowRight class="ml-2 h-4 w-4" />
                </Button>

                <button class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground" @click="handleSubmit">
                    <RefreshCw class="h-3.5 w-3.5" />
                    Renvoyer l'email
                </button>

                <ConditionsFooter />
            </div>

            <!-- Desktop -->
            <Card class="mt-6 hidden lg:block">
                <CardContent class="space-y-6 pt-6 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                        <CheckCircle class="h-6 w-6 text-primary" />
                    </div>

                    <div>
                        <h2 class="text-xl font-bold">Email envoyé !</h2>
                        <p class="mt-1 text-sm text-muted-foreground">Un lien de réinitialisation a été envoyé à</p>
                    </div>

                    <div class="inline-flex items-center gap-2 rounded-lg border px-4 py-2">
                        <Mail class="h-4 w-4 text-muted-foreground" />
                        <span class="text-sm font-medium">{{ maskedEmail(email) }}</span>
                    </div>

                    <p class="text-sm text-muted-foreground">
                        Cliquez sur le lien dans l'email pour créer un nouveau mot de passe. Le lien expire dans 15 minutes.
                    </p>

                    <Button class="w-full" size="lg" @click="emailSent = false">
                        Retour à la connexion
                        <ArrowRight class="ml-2 h-4 w-4" />
                    </Button>

                    <button class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground" @click="handleSubmit">
                        <RefreshCw class="h-3.5 w-3.5" />
                        Renvoyer l'email
                    </button>
                </CardContent>
            </Card>

            <div class="mt-4 hidden lg:block">
                <ConditionsFooter />
            </div>
        </template>
    </AuthLayout>
</template>
