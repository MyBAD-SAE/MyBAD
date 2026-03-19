<script setup>
import { ref, computed, nextTick } from 'vue';
import { Head, usePage, Link, router } from '@inertiajs/vue3';
import PlayerLayout from '@/Layouts/PlayerLayout.vue';
import BottomNavBar from '@/Components/BottomNavBar.vue';
import { Card, CardContent } from '@/Components/ui/card';
import {
    ArrowLeft,
    UserRound,
    Mail,
    Lock,
    ShieldCheck,
    Eye,
    EyeOff,
} from 'lucide-vue-next';

const user = usePage().props.auth.user;

// Identity & contact fields
const lastName = ref(user.last_name);
const firstName = ref(user.first_name);
const email = ref(user.email);

// PIN fields
const currentPin = ref(['', '', '', '']);
const newPin = ref(['', '', '', '']);
const confirmPin = ref(['', '', '', '']);
const showCurrentPin = ref(false);
const showNewPin = ref(false);
const showConfirmPin = ref(false);

// Password fields
const currentPassword = ref('');
const newPassword = ref('');
const confirmPassword = ref('');
const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

// Validation
const pinMismatch = computed(() => {
    const confirm = confirmPin.value.join('');
    const newP = newPin.value.join('');
    return confirm.length === 4 && newP.length === 4 && confirm !== newP;
});

const passwordMismatch = computed(() => {
    return confirmPassword.value.length > 0 && newPassword.value.length > 0 && confirmPassword.value !== newPassword.value;
});

function handlePinInput(pinArray, index, event) {
    const value = event.target.value.replace(/\D/g, '');
    pinArray[index] = value.slice(-1);
    if (value && index < 3) {
        const next = event.target.parentElement.children[index + 1];
        if (next) next.focus();
    }
}

function handlePinKeydown(pinArray, index, event) {
    if (event.key === 'Backspace' && !pinArray[index] && index > 0) {
        const prev = event.target.parentElement.children[index - 1];
        if (prev) prev.focus();
    }
}

// Success modal
const showSuccess = ref(false);
const progressWidth = ref(0);

function handleSave() {
    showSuccess.value = true;
    progressWidth.value = 0;

    nextTick(() => {
        requestAnimationFrame(() => {
            progressWidth.value = 100;
        });
    });

    setTimeout(() => {
        showSuccess.value = false;
        router.visit(route('profil.index'));
    }, 2500);
}
</script>

<template>
    <Head title="Infos Personnelles" />

    <PlayerLayout>
        <div class="pb-20">
            <!-- Header -->
            <div class="relative flex items-center justify-center px-5 pt-6 pb-5">
                <Link :href="route('profil.index')" class="absolute left-5 flex h-10 w-10 items-center justify-center rounded-2xl border border-border/50">
                    <ArrowLeft class="h-5 w-5 text-foreground" />
                </Link>
                <h1 class="text-lg font-bold text-foreground">Infos Personnelles</h1>
            </div>

            <!-- Identité -->
            <Card class="mx-4 gap-0 py-4">
                <CardContent class="space-y-4 px-4 py-0">
                    <span class="text-[11px] font-semibold uppercase tracking-widest text-muted-foreground/70">Identité</span>

                    <div>
                        <label class="text-[11px] font-semibold uppercase tracking-widest text-foreground">Nom</label>
                        <div class="mt-1.5 flex items-center gap-2.5 rounded-xl bg-muted/40 px-3.5 py-2">
                            <UserRound class="h-4 w-4 text-primary" />
                            <input v-model="lastName" type="text" class="flex-1 border-none bg-transparent text-sm text-muted-foreground/70 shadow-none outline-none ring-0 focus:ring-0" />
                        </div>
                    </div>

                    <div>
                        <label class="text-[11px] font-semibold uppercase tracking-widest text-foreground">Prénom</label>
                        <div class="mt-1.5 flex items-center gap-2.5 rounded-xl bg-muted/40 px-3.5 py-2">
                            <UserRound class="h-4 w-4 text-primary" />
                            <input v-model="firstName" type="text" class="flex-1 border-none bg-transparent text-sm text-muted-foreground/70 shadow-none outline-none ring-0 focus:ring-0" />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Contact -->
            <Card class="mx-4 mt-4 gap-0 py-4">
                <CardContent class="space-y-4 px-4 py-0">
                    <span class="text-[11px] font-semibold uppercase tracking-widest text-muted-foreground/70">Contact</span>

                    <div>
                        <label class="text-[11px] font-semibold uppercase tracking-widest text-foreground">Email</label>
                        <div class="mt-1.5 flex items-center gap-2.5 rounded-xl bg-muted/40 px-3.5 py-2">
                            <Mail class="h-4 w-4 text-violet-500" />
                            <input v-model="email" type="email" class="flex-1 border-none bg-transparent text-sm text-muted-foreground/70 shadow-none outline-none ring-0 focus:ring-0" />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Code PIN -->
            <Card class="mx-4 mt-4 gap-0 py-4">
                <CardContent class="space-y-5 px-4 py-0">
                    <div class="flex items-center gap-1.5">
                        <ShieldCheck class="h-4 w-4 text-primary" />
                        <span class="text-[11px] font-semibold uppercase tracking-widest text-muted-foreground/70">Code PIN</span>
                    </div>

                    <!-- Code PIN actuel -->
                    <div>
                        <label class="text-[11px] font-semibold uppercase tracking-widest text-foreground">Code PIN actuel</label>
                        <div class="mt-2 flex items-center">
                            <div class="flex gap-3">
                                <input
                                    v-for="(_, i) in 4"
                                    :key="'current-' + i"
                                    :type="showCurrentPin ? 'text' : 'password'"
                                    :value="currentPin[i]"
                                    maxlength="1"
                                    inputmode="numeric"
                                    class="h-14 w-14 rounded-2xl border border-border/50 bg-muted/30 text-center text-lg font-medium text-foreground outline-none focus:ring-2 focus:ring-primary/30"
                                    @input="handlePinInput(currentPin, i, $event)"
                                    @keydown="handlePinKeydown(currentPin, i, $event)"
                                />
                            </div>
                            <button class="ml-auto flex h-12 w-12 items-center justify-center rounded-2xl border border-border/50 bg-muted/30" @click="showCurrentPin = !showCurrentPin">
                                <component :is="showCurrentPin ? EyeOff : Eye" class="h-4 w-4 text-muted-foreground/60" />
                            </button>
                        </div>
                    </div>

                    <!-- Nouveau code PIN -->
                    <div>
                        <label class="text-[11px] font-semibold uppercase tracking-widest text-foreground">Nouveau code PIN</label>
                        <div class="mt-2 flex items-center">
                            <div class="flex gap-3">
                                <input
                                    v-for="(_, i) in 4"
                                    :key="'new-' + i"
                                    :type="showNewPin ? 'text' : 'password'"
                                    :value="newPin[i]"
                                    maxlength="1"
                                    inputmode="numeric"
                                    class="h-14 w-14 rounded-2xl border border-border/50 bg-muted/30 text-center text-lg font-medium text-foreground outline-none focus:ring-2 focus:ring-primary/30"
                                    @input="handlePinInput(newPin, i, $event)"
                                    @keydown="handlePinKeydown(newPin, i, $event)"
                                />
                            </div>
                            <button class="ml-auto flex h-12 w-12 items-center justify-center rounded-2xl border border-border/50 bg-muted/30" @click="showNewPin = !showNewPin">
                                <component :is="showNewPin ? EyeOff : Eye" class="h-4 w-4 text-muted-foreground/60" />
                            </button>
                        </div>
                    </div>

                    <!-- Confirmer code PIN -->
                    <div>
                        <label class="text-[11px] font-semibold uppercase tracking-widest text-foreground">Confirmer le code PIN</label>
                        <div class="mt-2 flex items-center">
                            <div class="flex gap-3">
                                <input
                                    v-for="(_, i) in 4"
                                    :key="'confirm-' + i"
                                    :type="showConfirmPin ? 'text' : 'password'"
                                    :value="confirmPin[i]"
                                    maxlength="1"
                                    inputmode="numeric"
                                    :class="[
                                        'h-14 w-14 rounded-2xl text-center text-lg font-medium text-foreground outline-none focus:ring-2 focus:ring-primary/30',
                                        pinMismatch ? 'border border-red-400 bg-red-50' : 'border border-border/50 bg-muted/30',
                                    ]"
                                    @input="handlePinInput(confirmPin, i, $event)"
                                    @keydown="handlePinKeydown(confirmPin, i, $event)"
                                />
                            </div>
                            <button class="ml-auto flex h-12 w-12 items-center justify-center rounded-2xl border border-border/50 bg-muted/30" @click="showConfirmPin = !showConfirmPin">
                                <component :is="showConfirmPin ? EyeOff : Eye" class="h-4 w-4 text-muted-foreground/60" />
                            </button>
                        </div>
                        <p v-if="pinMismatch" class="mt-1.5 text-xs text-red-500">Les codes PIN ne correspondent pas</p>
                    </div>
                </CardContent>
            </Card>

            <!-- Mot de passe -->
            <Card class="mx-4 mt-4 gap-0 py-4">
                <CardContent class="space-y-5 px-4 py-0">
                    <span class="text-[11px] font-semibold uppercase tracking-widest text-muted-foreground/70">Mot de passe</span>

                    <div>
                        <label class="text-[11px] font-semibold uppercase tracking-widest text-foreground">Mot de passe actuel</label>
                        <div class="mt-1.5 flex items-center gap-2.5 rounded-2xl border border-border/50 bg-muted/30 px-3.5 py-2">
                            <Lock class="h-4 w-4 text-amber-400" />
                            <input
                                v-model="currentPassword"
                                :type="showCurrentPassword ? 'text' : 'password'"
                                placeholder="••••••••••"
                                class="flex-1 border-none bg-transparent text-sm text-foreground shadow-none outline-none ring-0 placeholder:text-muted-foreground/50 focus:ring-0"
                            />
                            <button @click="showCurrentPassword = !showCurrentPassword">
                                <component :is="showCurrentPassword ? EyeOff : Eye" class="h-4 w-4 text-muted-foreground/50" />
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="text-[11px] font-semibold uppercase tracking-widest text-foreground">Nouveau mot de passe</label>
                        <div class="mt-1.5 flex items-center gap-2.5 rounded-2xl border border-border/50 bg-muted/30 px-3.5 py-2">
                            <Lock class="h-4 w-4 text-amber-400" />
                            <input
                                v-model="newPassword"
                                :type="showNewPassword ? 'text' : 'password'"
                                placeholder="••••••••••••"
                                class="flex-1 border-none bg-transparent text-sm text-foreground shadow-none outline-none ring-0 placeholder:text-muted-foreground/50 focus:ring-0"
                            />
                            <button @click="showNewPassword = !showNewPassword">
                                <component :is="showNewPassword ? EyeOff : Eye" class="h-4 w-4 text-muted-foreground/50" />
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="text-[11px] font-semibold uppercase tracking-widest text-foreground">Confirmer le mot de passe</label>
                        <div
                            :class="[
                                'mt-1.5 flex items-center gap-2.5 rounded-2xl px-3.5 py-2',
                                passwordMismatch ? 'border border-red-400 bg-red-50' : 'border border-border/50 bg-muted/30',
                            ]"
                        >
                            <Lock class="h-4 w-4 text-amber-400" />
                            <input
                                v-model="confirmPassword"
                                :type="showConfirmPassword ? 'text' : 'password'"
                                placeholder="••••••••••••"
                                class="flex-1 border-none bg-transparent text-sm text-foreground shadow-none outline-none ring-0 placeholder:text-muted-foreground/50 focus:ring-0"
                            />
                            <button @click="showConfirmPassword = !showConfirmPassword">
                                <component :is="showConfirmPassword ? EyeOff : Eye" class="h-4 w-4 text-muted-foreground/50" />
                            </button>
                        </div>
                        <p v-if="passwordMismatch" class="mt-1.5 text-xs text-red-500">Les mots de passe ne correspondent pas</p>
                    </div>
                </CardContent>
            </Card>

            <!-- Bouton enregistrer -->
            <div class="mx-4 mt-6">
                <button
                    class="w-full rounded-2xl bg-primary py-3.5 text-sm font-semibold text-white"
                    @click="handleSave"
                >
                    Enregistrer les modifications
                </button>
            </div>
        </div>

        <!-- Modal succès -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showSuccess" class="fixed inset-0 z-50 flex items-center justify-center">
                    <!-- Backdrop -->
                    <div class="absolute inset-0 bg-black/20 backdrop-blur-sm" />

                    <!-- Modal -->
                    <div class="relative mx-8 w-full max-w-sm rounded-3xl bg-white px-8 py-10 shadow-xl">
                        <div class="flex flex-col items-center text-center">
                            <!-- Icône check avec sparkles -->
                            <div class="relative">
                                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary">
                                    <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <!-- Sparkles -->
                                <svg class="absolute -right-2 -top-2 h-6 w-6 text-primary/60" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2l1.5 4.5L18 8l-4.5 1.5L12 14l-1.5-4.5L6 8l4.5-1.5z" />
                                </svg>
                                <svg class="absolute -bottom-1 -left-3 h-5 w-5 text-primary/40" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2l1.5 4.5L18 8l-4.5 1.5L12 14l-1.5-4.5L6 8l4.5-1.5z" />
                                </svg>
                            </div>

                            <h2 class="mt-5 text-lg font-bold text-foreground">Modifications enregistrées</h2>
                            <p class="mt-1 text-sm text-muted-foreground">Redirection vers votre profil...</p>

                            <!-- Barre de progression -->
                            <div class="mt-5 h-1.5 w-full overflow-hidden rounded-full bg-muted">
                                <div
                                    class="h-full rounded-full bg-primary transition-all duration-[2300ms] ease-linear"
                                    :style="{ width: progressWidth + '%' }"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <BottomNavBar active="profil" />
    </PlayerLayout>
</template>
