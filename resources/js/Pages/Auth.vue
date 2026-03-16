<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, reactive } from 'vue';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/Components/ui/tabs';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Checkbox } from '@/Components/ui/checkbox';
import { Separator } from '@/Components/ui/separator';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/card';
import { Mail, Lock, Eye, EyeOff, ArrowRight, User } from 'lucide-vue-next';

const activeTab = ref('login');

const loginForm = ref({
    email: '',
    password: '',
    remember: false,
});

const registerForm = ref({
    firstName: '',
    lastName: '',
    email: '',
    password: '',
    passwordConfirmation: '',
    acceptTerms: false,
});

const showLoginPassword = ref(false);
const showRegisterPassword = ref(false);
const showRegisterConfirmation = ref(false);

const isValidEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

const touched = reactive({
    loginEmail: false,
    loginPassword: false,
    registerFirstName: false,
    registerLastName: false,
    registerEmail: false,
    registerPassword: false,
    registerConfirmation: false,
});

const hasError = (field, value) => touched[field] && !value;
const hasEmailError = (field, value) => touched[field] && value && !isValidEmail(value);
const hasPasswordMinError = (field, value) => touched[field] && value && value.length < 8;
const hasConfirmError = () => touched.registerConfirmation && registerForm.value.passwordConfirmation && registerForm.value.passwordConfirmation !== registerForm.value.password;
</script>

<template>
    <Head title="Connexion" />

    <!-- Mobile layout -->
    <div class="min-h-screen bg-background lg:hidden">
        <!-- Hero -->
        <div class="relative h-56 overflow-hidden bg-gray-900">
            <img
                src="https://images.unsplash.com/photo-1613918431703-aa50889e3be9?w=800&q=80"
                alt="Terrain de badminton"
                class="absolute inset-0 h-full w-full object-cover opacity-60"
            />
            <div class="absolute inset-0 bg-gradient-to-b from-black/40 to-black/60" />
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

        <!-- Form -->
        <div class="px-6 py-6">
            <Tabs v-model="activeTab" class="w-full">
                <TabsList class="grid w-full grid-cols-2">
                    <TabsTrigger value="login">Connexion</TabsTrigger>
                    <TabsTrigger value="register">Inscription</TabsTrigger>
                </TabsList>

                <!-- Login -->
                <TabsContent value="login" class="mt-6 space-y-6">
                    <p class="text-sm text-muted-foreground">
                        Connectez-vous pour retrouver votre dashboard
                    </p>

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label for="login-email">Adresse email</Label>
                            <div class="relative">
                                <Mail class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    id="login-email"
                                    v-model="loginForm.email"
                                    type="email"
                                    placeholder="nom@exemple.com"
                                    class="pl-10"
                                    :aria-invalid="hasEmailError('loginEmail', loginForm.email) || hasError('loginEmail', loginForm.email)"
                                    @blur="touched.loginEmail = true"
                                />
                            </div>
                            <p v-if="hasError('loginEmail', loginForm.email)" class="text-sm text-destructive">L'email est requis.</p>
                            <p v-else-if="hasEmailError('loginEmail', loginForm.email)" class="text-sm text-destructive">Format d'email invalide.</p>
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
                                    v-model="loginForm.password"
                                    :type="showLoginPassword ? 'text' : 'password'"
                                    placeholder="Votre mot de passe"
                                    class="pl-10 pr-10"
                                    :aria-invalid="hasError('loginPassword', loginForm.password)"
                                    @blur="touched.loginPassword = true"
                                />
                                <button
                                    type="button"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                                    @click="showLoginPassword = !showLoginPassword"
                                >
                                    <EyeOff v-if="showLoginPassword" class="h-4 w-4" />
                                    <Eye v-else class="h-4 w-4" />
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <Checkbox
                                id="remember"
                                :checked="loginForm.remember"
                                @update:checked="loginForm.remember = $event"
                            />
                            <Label for="remember" class="text-sm font-normal">Se souvenir de moi</Label>
                        </div>
                    </div>

                    <Button class="w-full" size="lg">
                        Se connecter
                        <ArrowRight class="ml-2 h-4 w-4" />
                    </Button>

                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <Separator class="w-full" />
                        </div>
                        <div class="relative flex justify-center text-xs uppercase">
                            <span class="bg-background px-2 text-muted-foreground">ou continuer avec</span>
                        </div>
                    </div>

                    <Button variant="outline" class="w-full" size="lg">
                        <svg class="mr-2 h-4 w-4" viewBox="0 0 24 24">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4" />
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                        </svg>
                        Continuer avec Google
                    </Button>

                    <p class="text-center text-xs text-muted-foreground">
                        En continuant, vous acceptez nos
                        <button class="underline hover:text-foreground">conditions d'utilisation</button>.
                    </p>
                </TabsContent>

                <!-- Register -->
                <TabsContent value="register" class="mt-6 space-y-6">
                    <div>
                        <h2 class="text-xl font-bold">Créer un compte</h2>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Rejoignez la communauté MyBAD en quelques secondes
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="register-firstname">Prénom</Label>
                                <div class="relative">
                                    <User class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                    <Input
                                        id="register-firstname"
                                        v-model="registerForm.firstName"
                                        placeholder="Lucas"
                                        class="pl-10"
                                        :aria-invalid="hasError('registerFirstName', registerForm.firstName)"
                                        @blur="touched.registerFirstName = true"
                                    />
                                </div>
                                <p v-if="hasError('registerFirstName', registerForm.firstName)" class="text-sm text-destructive">Le prénom est requis.</p>
                            </div>
                            <div class="space-y-2">
                                <Label for="register-lastname">Nom</Label>
                                <Input
                                    id="register-lastname"
                                    v-model="registerForm.lastName"
                                    placeholder="Torres"
                                    :aria-invalid="hasError('registerLastName', registerForm.lastName)"
                                    @blur="touched.registerLastName = true"
                                />
                                <p v-if="hasError('registerLastName', registerForm.lastName)" class="text-sm text-destructive">Le nom est requis.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="register-email">Email</Label>
                            <div class="relative">
                                <Mail class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    id="register-email"
                                    v-model="registerForm.email"
                                    type="email"
                                    placeholder="nom@exemple.com"
                                    class="pl-10"
                                    :aria-invalid="hasEmailError('registerEmail', registerForm.email) || hasError('registerEmail', registerForm.email)"
                                    @blur="touched.registerEmail = true"
                                />
                            </div>
                            <p v-if="hasError('registerEmail', registerForm.email)" class="text-sm text-destructive">L'email est requis.</p>
                            <p v-else-if="hasEmailError('registerEmail', registerForm.email)" class="text-sm text-destructive">Format d'email invalide.</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="register-password">Mot de passe</Label>
                            <div class="relative">
                                <Lock class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    id="register-password"
                                    v-model="registerForm.password"
                                    :type="showRegisterPassword ? 'text' : 'password'"
                                    placeholder="Min. 8 caractères"
                                    class="pl-10 pr-10"
                                    :aria-invalid="hasPasswordMinError('registerPassword', registerForm.password)"
                                    @blur="touched.registerPassword = true"
                                />
                                <button
                                    type="button"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                                    @click="showRegisterPassword = !showRegisterPassword"
                                >
                                    <EyeOff v-if="showRegisterPassword" class="h-4 w-4" />
                                    <Eye v-else class="h-4 w-4" />
                                </button>
                            </div>
                            <p v-if="hasPasswordMinError('registerPassword', registerForm.password)" class="text-sm text-destructive">Le mot de passe doit contenir au moins 8 caractères.</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="register-confirm">Confirmer</Label>
                            <div class="relative">
                                <Lock class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    id="register-confirm"
                                    v-model="registerForm.passwordConfirmation"
                                    :type="showRegisterConfirmation ? 'text' : 'password'"
                                    placeholder="Retapez le mot de passe"
                                    class="pl-10 pr-10"
                                    :aria-invalid="hasConfirmError()"
                                    @blur="touched.registerConfirmation = true"
                                />
                                <button
                                    type="button"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                                    @click="showRegisterConfirmation = !showRegisterConfirmation"
                                >
                                    <EyeOff v-if="showRegisterConfirmation" class="h-4 w-4" />
                                    <Eye v-else class="h-4 w-4" />
                                </button>
                            </div>
                            <p v-if="hasConfirmError()" class="text-sm text-destructive">Les mots de passe ne correspondent pas.</p>
                        </div>

                        <div class="flex items-start gap-2">
                            <Checkbox
                                id="terms"
                                :checked="registerForm.acceptTerms"
                                @update:checked="registerForm.acceptTerms = $event"
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

                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <Separator class="w-full" />
                        </div>
                        <div class="relative flex justify-center text-xs uppercase">
                            <span class="bg-background px-2 text-muted-foreground">ou continuer avec</span>
                        </div>
                    </div>

                    <Button variant="outline" class="w-full" size="lg">
                        <svg class="mr-2 h-4 w-4" viewBox="0 0 24 24">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4" />
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                        </svg>
                        Continuer avec Google
                    </Button>
                </TabsContent>
            </Tabs>
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
                    <img src="/images/logo_mybad.png" alt="MyBAD" class="h-10 w-10 rounded-md" />
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

        <!-- Right: Form -->
        <div class="flex w-3/5 flex-col items-center justify-start overflow-y-auto px-8 py-10">
            <div class="w-full max-w-md">
                <Tabs v-model="activeTab" class="w-full">
                    <div class="flex justify-center">
                        <TabsList class="grid w-64 grid-cols-2">
                            <TabsTrigger value="login">Connexion</TabsTrigger>
                            <TabsTrigger value="register">Inscription</TabsTrigger>
                        </TabsList>
                    </div>

                    <!-- Login -->
                    <TabsContent value="login" class="mt-8">
                        <Card>
                            <CardHeader>
                                <CardTitle class="text-xl">Bon retour parmi nous</CardTitle>
                                <CardDescription>Connectez-vous pour accéder à votre dashboard</CardDescription>
                            </CardHeader>
                            <CardContent class="space-y-6">
                                <div class="space-y-4">
                                    <div class="space-y-2">
                                        <Label for="desktop-login-email">Email</Label>
                                        <div class="relative">
                                            <Mail class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                            <Input
                                                id="desktop-login-email"
                                                v-model="loginForm.email"
                                                type="email"
                                                placeholder="nom@exemple.com"
                                                class="pl-10"
                                                :aria-invalid="hasEmailError('loginEmail', loginForm.email) || hasError('loginEmail', loginForm.email)"
                                                @blur="touched.loginEmail = true"
                                            />
                                        </div>
                                        <p v-if="hasError('loginEmail', loginForm.email)" class="text-sm text-destructive">L'email est requis.</p>
                                        <p v-else-if="hasEmailError('loginEmail', loginForm.email)" class="text-sm text-destructive">Format d'email invalide.</p>
                                    </div>

                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <Label for="desktop-login-password">Mot de passe</Label>
                                            <Link :href="route('password.request')" class="text-sm font-medium text-primary hover:underline">
                                                Mot de passe oublié ?
                                            </Link>
                                        </div>
                                        <div class="relative">
                                            <Lock class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                            <Input
                                                id="desktop-login-password"
                                                v-model="loginForm.password"
                                                :type="showLoginPassword ? 'text' : 'password'"
                                                placeholder="Entrez votre mot de passe"
                                                class="pl-10 pr-10"
                                                :aria-invalid="hasError('loginPassword', loginForm.password)"
                                                @blur="touched.loginPassword = true"
                                            />
                                            <button
                                                type="button"
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                                                @click="showLoginPassword = !showLoginPassword"
                                            >
                                                <EyeOff v-if="showLoginPassword" class="h-4 w-4" />
                                                <Eye v-else class="h-4 w-4" />
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <Checkbox
                                            id="desktop-remember"
                                            :checked="loginForm.remember"
                                            @update:checked="loginForm.remember = $event"
                                        />
                                        <Label for="desktop-remember" class="text-sm font-normal">Se souvenir de moi</Label>
                                    </div>
                                </div>

                                <Button class="w-full" size="lg">
                                    Se connecter
                                    <ArrowRight class="ml-2 h-4 w-4" />
                                </Button>

                                <div class="relative">
                                    <div class="absolute inset-0 flex items-center">
                                        <Separator class="w-full" />
                                    </div>
                                    <div class="relative flex justify-center text-xs uppercase">
                                        <span class="bg-card px-2 text-muted-foreground">ou continuer avec</span>
                                    </div>
                                </div>

                                <Button variant="outline" class="w-full" size="lg">
                                    <svg class="mr-2 h-4 w-4" viewBox="0 0 24 24">
                                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4" />
                                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                                    </svg>
                                    Google
                                </Button>
                            </CardContent>
                        </Card>

                        <p class="mt-4 text-center text-xs text-muted-foreground">
                            En continuant, vous acceptez nos
                            <button class="underline hover:text-foreground">conditions d'utilisation</button>.
                        </p>
                    </TabsContent>

                    <!-- Register -->
                    <TabsContent value="register" class="mt-8">
                        <Card>
                            <CardHeader>
                                <CardTitle class="text-xl">Créer un compte</CardTitle>
                                <CardDescription>Rejoignez la communauté en quelques secondes</CardDescription>
                            </CardHeader>
                            <CardContent class="space-y-6">
                                <div class="space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <Label for="desktop-register-firstname">Prénom</Label>
                                            <div class="relative">
                                                <User class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                                <Input
                                                    id="desktop-register-firstname"
                                                    v-model="registerForm.firstName"
                                                    placeholder="Jean"
                                                    class="pl-10"
                                                    :aria-invalid="hasError('registerFirstName', registerForm.firstName)"
                                                    @blur="touched.registerFirstName = true"
                                                />
                                            </div>
                                            <p v-if="hasError('registerFirstName', registerForm.firstName)" class="text-sm text-destructive">Le prénom est requis.</p>
                                        </div>
                                        <div class="space-y-2">
                                            <Label for="desktop-register-lastname">Nom</Label>
                                            <Input
                                                id="desktop-register-lastname"
                                                v-model="registerForm.lastName"
                                                placeholder="DUPONT"
                                                :aria-invalid="hasError('registerLastName', registerForm.lastName)"
                                                @blur="touched.registerLastName = true"
                                            />
                                            <p v-if="hasError('registerLastName', registerForm.lastName)" class="text-sm text-destructive">Le nom est requis.</p>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="desktop-register-email">Email</Label>
                                        <div class="relative">
                                            <Mail class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                            <Input
                                                id="desktop-register-email"
                                                v-model="registerForm.email"
                                                type="email"
                                                placeholder="nom@exemple.com"
                                                class="pl-10"
                                                :aria-invalid="hasEmailError('registerEmail', registerForm.email) || hasError('registerEmail', registerForm.email)"
                                                @blur="touched.registerEmail = true"
                                            />
                                        </div>
                                        <p v-if="hasError('registerEmail', registerForm.email)" class="text-sm text-destructive">L'email est requis.</p>
                                        <p v-else-if="hasEmailError('registerEmail', registerForm.email)" class="text-sm text-destructive">Format d'email invalide.</p>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="desktop-register-password">Mot de passe</Label>
                                        <div class="relative">
                                            <Lock class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                            <Input
                                                id="desktop-register-password"
                                                v-model="registerForm.password"
                                                :type="showRegisterPassword ? 'text' : 'password'"
                                                placeholder="Minimum 8 caractères"
                                                class="pl-10 pr-10"
                                                :aria-invalid="hasPasswordMinError('registerPassword', registerForm.password)"
                                                @blur="touched.registerPassword = true"
                                            />
                                            <button
                                                type="button"
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                                                @click="showRegisterPassword = !showRegisterPassword"
                                            >
                                                <EyeOff v-if="showRegisterPassword" class="h-4 w-4" />
                                                <Eye v-else class="h-4 w-4" />
                                            </button>
                                        </div>
                                        <p v-if="hasPasswordMinError('registerPassword', registerForm.password)" class="text-sm text-destructive">Le mot de passe doit contenir au moins 8 caractères.</p>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="desktop-register-confirm">Confirmer le mot de passe</Label>
                                        <div class="relative">
                                            <Lock class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                            <Input
                                                id="desktop-register-confirm"
                                                v-model="registerForm.passwordConfirmation"
                                                :type="showRegisterConfirmation ? 'text' : 'password'"
                                                placeholder="Confirmez votre mot de passe"
                                                class="pl-10 pr-10"
                                                :aria-invalid="hasConfirmError()"
                                                @blur="touched.registerConfirmation = true"
                                            />
                                            <button
                                                type="button"
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                                                @click="showRegisterConfirmation = !showRegisterConfirmation"
                                            >
                                                <EyeOff v-if="showRegisterConfirmation" class="h-4 w-4" />
                                                <Eye v-else class="h-4 w-4" />
                                            </button>
                                        </div>
                                        <p v-if="hasConfirmError()" class="text-sm text-destructive">Les mots de passe ne correspondent pas.</p>
                                    </div>
                                </div>

                                <Button class="w-full" size="lg">
                                    Créer mon compte
                                    <ArrowRight class="ml-2 h-4 w-4" />
                                </Button>

                                <div class="relative">
                                    <div class="absolute inset-0 flex items-center">
                                        <Separator class="w-full" />
                                    </div>
                                    <div class="relative flex justify-center text-xs uppercase">
                                        <span class="bg-card px-2 text-muted-foreground">ou continuer avec</span>
                                    </div>
                                </div>

                                <Button variant="outline" class="w-full" size="lg">
                                    <svg class="mr-2 h-4 w-4" viewBox="0 0 24 24">
                                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4" />
                                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                                    </svg>
                                    Google
                                </Button>
                            </CardContent>
                        </Card>

                        <p class="mt-4 text-center text-xs text-muted-foreground">
                            En continuant, vous acceptez nos
                            <button class="underline hover:text-foreground">conditions d'utilisation</button>.
                        </p>
                    </TabsContent>
                </Tabs>
            </div>
        </div>
    </div>
</template>
