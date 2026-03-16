<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Card, CardContent } from '@/Components/ui/card';
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

    <!-- Mobile layout -->
    <div class="min-h-screen bg-background lg:hidden">
        <!-- Hero -->
        <div class="relative h-48 overflow-hidden bg-gray-900">
            <img
                src="https://images.unsplash.com/photo-1613918431703-aa50889e3be9?w=800&q=80"
                alt="Terrain de badminton"
                class="absolute inset-0 h-full w-full object-cover opacity-60"
            />
            <div class="absolute inset-0 bg-gradient-to-b from-black/40 to-black/60" />
            <div class="relative z-10 flex h-full flex-col justify-between p-6">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🏸</span>
                    <span class="text-lg font-bold text-white">MyBAD</span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">Réinitialiser votre mot de passe</h1>
                    <p class="mt-1 text-sm text-white/80">On vous envoie un lien par email.</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-6 py-6">
            <Link :href="route('auth')" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="h-4 w-4" />
                Retour à la connexion
            </Link>

            <!-- Form state -->
            <div v-if="!emailSent" class="mt-6 space-y-6">
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
                    <Label for="mobile-email">Adresse email</Label>
                    <div class="relative">
                        <Mail class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            id="mobile-email"
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

                <p class="text-xs text-muted-foreground">
                    En continuant, vous acceptez nos
                    <button class="underline hover:text-foreground">conditions d'utilisation</button>.
                </p>
            </div>

            <!-- Email sent state -->
            <div v-else class="mt-6 space-y-6 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                    <CheckCircle class="h-6 w-6 text-primary" />
                </div>

                <div>
                    <h2 class="text-xl font-bold">Email envoyé !</h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Un lien de réinitialisation a été envoyé à
                    </p>
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

                <p class="text-xs text-muted-foreground">
                    En continuant, vous acceptez nos
                    <button class="underline hover:text-foreground">conditions d'utilisation</button>.
                </p>
            </div>
        </div>
    </div>

    <!-- Desktop layout -->
    <div class="hidden min-h-screen lg:flex">
        <!-- Left: Hero image -->
        <div class="relative w-2/5 bg-gray-900">
            <img
                src="https://images.unsplash.com/photo-1613918431703-aa50889e3be9?w=1200&q=80"
                alt="Terrain de badminton"
                class="absolute inset-0 h-full w-full object-cover opacity-60"
            />
            <div class="absolute inset-0 bg-gradient-to-r from-black/50 to-black/30" />
            <div class="relative z-10 flex h-full flex-col justify-between p-10">
                <div class="flex items-center gap-2">
                    <span class="text-3xl">🏸</span>
                    <span class="text-xl font-bold text-white">MyBAD</span>
                </div>
                <div class="mb-16">
                    <h1 class="text-4xl font-bold leading-tight text-white">
                        Suivez vos performances<br />au badminton
                    </h1>
                    <p class="mt-3 max-w-sm text-base text-white/80">
                        Classement ELO, statistiques de matchs, suivi de progression.
                        Tout ce qu'il faut pour devenir le meilleur joueur de votre club.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right: Content -->
        <div class="flex w-3/5 flex-col items-center justify-start overflow-y-auto px-8 py-10">
            <div class="w-full max-w-md">
                <Link :href="route('auth')" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground">
                    <ArrowLeft class="h-4 w-4" />
                    Retour à la connexion
                </Link>

                <!-- Form state -->
                <Card v-if="!emailSent" class="mt-6">
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
                            <Label for="desktop-email">Adresse email</Label>
                            <div class="relative">
                                <Mail class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    id="desktop-email"
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

                        <Alert class="border-primary/20 bg-primary/5">
                            <AlertDescription class="text-sm text-muted-foreground">
                                Le lien de réinitialisation expirera après <strong class="text-foreground">15 minutes</strong>. Pensez aussi à vérifier vos spams.
                            </AlertDescription>
                        </Alert>
                    </CardContent>
                </Card>

                <!-- Email sent state -->
                <Card v-else class="mt-6">
                    <CardContent class="space-y-6 pt-6 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                            <CheckCircle class="h-6 w-6 text-primary" />
                        </div>

                        <div>
                            <h2 class="text-xl font-bold">Email envoyé !</h2>
                            <p class="mt-1 text-sm text-muted-foreground">
                                Un lien de réinitialisation a été envoyé à
                            </p>
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

                <p class="mt-4 text-center text-xs text-muted-foreground">
                    En continuant, vous acceptez nos
                    <button class="underline hover:text-foreground">conditions d'utilisation</button>.
                </p>
            </div>
        </div>
    </div>
</template>
