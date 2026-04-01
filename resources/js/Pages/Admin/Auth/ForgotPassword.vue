<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Card, CardContent } from '@/Components/ui/card';
import { Mail, ArrowLeft, ArrowRight, CheckCircle, RefreshCw, LoaderCircle } from 'lucide-vue-next';

const props = defineProps({
    flash: Object,
});

const form = useForm({
    email: '',
});

const emailSent = computed(() => props.flash?.success === true);
const emailTouched = ref(false);

const isValidEmail = (val) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
const emailEmpty = () => emailTouched.value && !form.email;
const emailInvalid = () => emailTouched.value && form.email && !isValidEmail(form.email);

function handleSubmit() {
    emailTouched.value = true;
    if (form.email && isValidEmail(form.email)) {
        form.post(route('admin.password.email'), {
            preserveScroll: true,
        });
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
        title="Réinitialisez votre mot de passe"
        subtitle="On vous envoie un lien par email."
        image="/images/muktasim-azlan-tkVwhG6yqKo-unsplash.jpg"
    >
        <Link :href="route('admin.login')" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground">
            <ArrowLeft class="h-4 w-4" />
            Retour à la connexion
        </Link>

        <template v-if="!emailSent">
            <!-- Mobile -->
            <div class="mt-6 space-y-6 lg:hidden">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <Mail class="h-5 w-5 text-primary" />
                </div>

                <div>
                    <h2 class="text-xl font-bold">Mot de passe oublié ?</h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Entrez votre email et nous vous enverrons un lien de réinitialisation.
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="forgot-email">Adresse email</Label>
                    <div class="relative">
                        <Mail class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            id="forgot-email"
                            v-model="form.email"
                            type="email"
                            placeholder="nom@exemple.com"
                            class="pl-10"
                            :aria-invalid="emailEmpty() || emailInvalid()"
                            @blur="emailTouched = true"
                        />
                    </div>
                    <p v-if="emailEmpty()" class="text-sm text-destructive">L'email est requis.</p>
                    <p v-else-if="emailInvalid()" class="text-sm text-destructive">Format d'email invalide.</p>
                    <p v-else-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
                </div>

                <Button class="w-full" size="lg" :disabled="form.processing" @click="handleSubmit">
                    <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                    Envoyer le lien
                    <ArrowRight v-if="!form.processing" class="ml-2 h-4 w-4" />
                </Button>

                <div class="rounded-2xl bg-muted px-5 py-4 text-xs text-muted-foreground">
                    Le lien de réinitialisation expirera après <strong class="font-semibold text-foreground">15 minutes</strong>. Pensez aussi à vérifier vos spams.
                </div>
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
                            Entrez votre email et nous vous enverrons un lien de réinitialisation.
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="forgot-email-desktop">Adresse email</Label>
                        <div class="relative">
                            <Mail class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                id="forgot-email-desktop"
                                v-model="form.email"
                                type="email"
                                placeholder="nom@exemple.com"
                                class="pl-10"
                                :aria-invalid="emailEmpty() || emailInvalid()"
                                @blur="emailTouched = true"
                            />
                        </div>
                        <p v-if="emailEmpty()" class="text-sm text-destructive">L'email est requis.</p>
                        <p v-else-if="emailInvalid()" class="text-sm text-destructive">Format d'email invalide.</p>
                        <p v-else-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
                    </div>

                    <Button class="w-full" size="lg" :disabled="form.processing" @click="handleSubmit">
                        <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        Envoyer le lien
                        <ArrowRight v-if="!form.processing" class="ml-2 h-4 w-4" />
                    </Button>

                    <div class="rounded-lg border border-border px-4 py-3 text-sm text-muted-foreground">
                        Le lien de réinitialisation expirera après <strong class="font-semibold text-foreground">15 minutes</strong>. Pensez aussi à vérifier vos spams.
                    </div>
                </CardContent>
            </Card>
        </template>

        <!-- Email envoyé -->
        <template v-else>
            <div class="mt-8 flex flex-col items-center text-center">
                <div class="relative">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary">
                        <Mail class="h-7 w-7 text-white" />
                    </div>
                    <div class="absolute -right-1 -top-1 flex h-7 w-7 items-center justify-center rounded-full bg-white shadow-md">
                        <CheckCircle class="h-5 w-5 text-primary" />
                    </div>
                </div>

                <h2 class="mt-6 text-xl font-bold text-foreground">Email envoyé !</h2>
                <p class="mt-2 text-sm text-muted-foreground">
                    Un lien de réinitialisation a été envoyé à
                </p>

                <div class="mt-3 inline-flex items-center gap-2.5 rounded-2xl bg-primary/10 px-5 py-2.5">
                    <Mail class="h-4 w-4 text-primary" />
                    <span class="text-sm font-semibold text-primary">{{ maskedEmail(form.email) }}</span>
                </div>

                <p class="mt-4 text-xs text-muted-foreground/70">
                    Le lien expire dans <strong class="font-semibold text-muted-foreground">15 minutes</strong>. Pensez à vérifier vos spams.
                </p>

                <div class="mt-6 w-full space-y-3">
                    <Link :href="route('admin.login')" class="block w-full">
                        <Button class="w-full" size="lg">
                            Retour à la connexion
                            <ArrowRight class="ml-2 h-4 w-4" />
                        </Button>
                    </Link>

                    <button
                        class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-border/50 bg-white py-2.5 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted/50 hover:text-foreground"
                        :disabled="form.processing"
                        @click="handleSubmit"
                    >
                        <LoaderCircle v-if="form.processing" class="h-3.5 w-3.5 animate-spin" />
                        <RefreshCw v-else class="h-3.5 w-3.5" />
                        Renvoyer l'email
                    </button>
                </div>
            </div>
        </template>
    </AuthLayout>
</template>
