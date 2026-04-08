<script setup>
import { ref, computed, nextTick, watch } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import PlayerLayout from '@/Layouts/PlayerLayout.vue';
import BottomNavBar from '@/Components/player/BottomNavBar.vue';
import { Avatar, AvatarImage, AvatarFallback } from '@/Components/ui/avatar';
import { Card, CardContent } from '@/Components/ui/card';
import {
    ArrowLeft,
    UserRound,
    Mail,
    Lock,
    Eye,
    EyeOff,
    Camera,
} from 'lucide-vue-next';

const props = defineProps({
    user: { type: Object, required: true },
});

const photoInput = ref(null);
const photoPreview = ref(null);
const photoUploading = ref(false);
const photoError = ref(null);

function selectPhoto() {
    photoInput.value.click();
}

function uploadPhoto(event) {
    const file = event.target.files[0];
    if (!file) return;

    photoError.value = null;

const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        photoError.value = 'L\'image doit être au format JPG, PNG ou WebP.';
        photoInput.value.value = '';
        return;
    }

    photoPreview.value = URL.createObjectURL(file);
    photoUploading.value = true;

    router.post(route('player.account.photo.update'), { photo: file }, {
        forceFormData: true,
        preserveScroll: true,
        onError: (errors) => {
            photoError.value = errors.photo;
            photoPreview.value = null;
        },
        onFinish: () => {
            photoUploading.value = false;
            photoInput.value.value = '';
        },
    });
}

// Form
const form = useForm({
    first_name: props.user.first_name,
    last_name: props.user.last_name,
    email: props.user.email,
    current_pin: '',
    new_pin: '',
    new_password_confirmation: '',
    current_password: '',
    new_password: '',
});

watch(
    () => props.user,
    (newValue) => {
        form.first_name = newValue.first_name;
        form.last_name  = newValue.last_name;
        form.email      = newValue.email;
    }
);

// PIN fields (UI arrays)
const currentPin = ref(['', '', '', '']);
const newPin = ref(['', '', '', '']);
const confirmPin = ref(['', '', '', '']);
const showCurrentPin = ref(false);
const showNewPin = ref(false);
const showConfirmPin = ref(false);

// Password fields visibility
const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

// Password strength
const passwordStrength = computed(() => {
    const p = form.new_password;
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

// Validation
const pinMismatch = computed(() => {
    const confirm = confirmPin.value.join('');
    const newP = newPin.value.join('');
    return confirm.length === 4 && newP.length === 4 && confirm !== newP;
});

const passwordMismatch = computed(() => {
    return form.new_password_confirmation.length > 0 && form.new_password.length > 0 && form.new_password_confirmation !== form.new_password;
});

const saveDisabled = computed(() => {
    const currentStr = currentPin.value.join('');
    const newStr = newPin.value.join('');
    const confirmStr = confirmPin.value.join('');

    // PIN partiellement rempli
    if (currentStr.length > 0 && currentStr.length < 4) return true;
    if (newStr.length > 0 && newStr.length < 4) return true;
    if (confirmStr.length > 0 && confirmStr.length < 4) return true;

    // PIN ne correspondent pas
    if (pinMismatch.value) return true;

    // Nouveau PIN sans confirmation ou sans PIN actuel
    if (newStr.length === 4 && confirmStr.length !== 4) return true;
    if (newStr.length === 4 && currentStr.length !== 4) return true;

    // Mots de passe
    if (passwordMismatch.value) return true;
    if (form.new_password && !form.new_password_confirmation) return true;
    if (form.new_password_confirmation && !form.new_password) return true;
    if (form.new_password && !form.current_password) return true;

    return false;
});

function handlePinInput(pinArray, index, event) {
    const value = event.target.value.replace(/\D/g, '');
    event.target.value = value.slice(-1);
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
    // Bloquer tout sauf chiffres, Backspace, Tab, flèches
    if (!/^\d$/.test(event.key) && !['Backspace', 'Tab', 'ArrowLeft', 'ArrowRight', 'Delete'].includes(event.key)) {
        event.preventDefault();
    }
}

// Success modal
const showSuccess = ref(false);
const progressWidth = ref(0);
let successTimer = null;

function handleSave() {
    const currentPinStr = currentPin.value.join('');
    const newPinStr = newPin.value.join('');
    if (pinMismatch.value) return;

    form.current_pin = currentPinStr.length === 4 ? currentPinStr : null;
    form.new_pin = newPinStr.length === 4 ? newPinStr : null;

    form.put(route('player.account.infos.update'), {
        onSuccess: () => {
            form.reset('current_pin', 'new_pin', 'current_password', 'new_password', 'new_password_confirmation');
            currentPin.value = ['', '', '', ''];
            newPin.value = ['', '', '', ''];
            confirmPin.value = ['', '', '', ''];

            // Reset previous timer if still running
            if (successTimer) clearTimeout(successTimer);
            showSuccess.value = false;
            progressWidth.value = 0;

            nextTick(() => {
                showSuccess.value = true;
                requestAnimationFrame(() => {
                    progressWidth.value = 100;
                });

                successTimer = setTimeout(() => {
                    showSuccess.value = false;
                }, 2500);
            });
        },
    });
}
</script>

<template>
    <Head title="Informations personnelles" />

    <PlayerLayout>
        <div class="pb-20">
            <!-- Header -->
            <div class="relative flex items-center justify-center px-5 pt-6 pb-5">
                <Link :href="route('player.account.index')" class="absolute left-5 flex h-10 w-10 items-center justify-center rounded-2xl border border-border/50">
                    <ArrowLeft class="h-5 w-5 text-foreground" />
                </Link>
                <h1 class="text-lg font-bold text-foreground">Informations personnelles</h1>
            </div>

            <!-- Photo de profil -->
            <div class="flex flex-col items-center py-4">
                <div class="relative">
                    <Avatar class="h-24 w-24 border-4 border-background shadow-lg">
                        <AvatarImage v-if="photoPreview || user?.profile_picture" :src="photoPreview || user.profile_picture" :alt="user?.first_name" />
                        <AvatarFallback class="text-2xl">{{ user?.first_name?.charAt(0) }}</AvatarFallback>
                    </Avatar>
                    <button
                        type="button"
                        class="absolute bottom-0 right-0 flex h-8 w-8 items-center justify-center rounded-full bg-primary text-white shadow-md"
                        :class="{ 'opacity-50': photoUploading }"
                        :disabled="photoUploading"
                        @click="selectPhoto"
                    >
                        <Camera class="h-4 w-4" />
                    </button>
                </div>
                <input ref="photoInput" type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="uploadPhoto" />
                <p v-if="photoError" class="mt-2 text-xs text-destructive">{{ photoError }}</p>
                <p v-else class="mt-2 text-xs text-muted-foreground">Changer la photo de profil</p>
            </div>

            <!-- Identité -->
            <Card class="mx-4 gap-0 py-4 shadow-none">
                <CardContent class="space-y-4 px-4 py-0">
                    <span class="text-[11px] font-semibold uppercase tracking-widest text-muted-foreground/70">Identité</span>

                    <div>
                        <label class="text-[11px] font-semibold uppercase tracking-widest text-foreground">Nom</label>
                        <div class="mt-1.5 flex items-center gap-2.5 rounded-xl border border-transparent bg-muted/40 px-3.5 py-2 transition-colors focus-within:border-primary focus-within:ring-[3px] focus-within:ring-primary/20">
                            <UserRound class="h-4 w-4 shrink-0 text-primary" />
                            <input v-model="form.last_name" type="text" class="flex-1 border-none bg-transparent text-sm text-foreground shadow-none outline-none ring-0 focus:ring-0" />
                        </div>
                        <p v-if="form.errors.last_name" class="mt-1.5 text-xs text-destructive">{{ form.errors.last_name }}</p>
                    </div>

                    <div>
                        <label class="text-[11px] font-semibold uppercase tracking-widest text-foreground">Prénom</label>
                        <div class="mt-1.5 flex items-center gap-2.5 rounded-xl border border-transparent bg-muted/40 px-3.5 py-2 transition-colors focus-within:border-primary focus-within:ring-[3px] focus-within:ring-primary/20">
                            <UserRound class="h-4 w-4 shrink-0 text-primary" />
                            <input v-model="form.first_name" type="text" class="flex-1 border-none bg-transparent text-sm text-foreground shadow-none outline-none ring-0 focus:ring-0" />
                        </div>
                        <p v-if="form.errors.first_name" class="mt-1.5 text-xs text-destructive">{{ form.errors.first_name }}</p>
                    </div>
                </CardContent>
            </Card>

            <!-- Contact -->
            <Card class="mx-4 mt-4 gap-0 py-4 shadow-none">
                <CardContent class="space-y-4 px-4 py-0">
                    <span class="text-[11px] font-semibold uppercase tracking-widest text-muted-foreground/70">Contact</span>
                    <div>
                        <label class="text-[11px] font-semibold uppercase tracking-widest text-foreground">Email</label>
                        <div class="mt-1.5 flex items-center gap-2.5 rounded-xl border border-transparent bg-muted/40 px-3.5 py-2 transition-colors focus-within:border-primary focus-within:ring-[3px] focus-within:ring-primary/20">
                            <Mail class="h-4 w-4 shrink-0 text-violet-500" />
                            <input v-model="form.email" type="email" class="flex-1 border-none bg-transparent text-sm text-foreground shadow-none outline-none ring-0 focus:ring-0" />
                        </div>
                        <p v-if="form.errors.email" class="mt-1.5 text-xs text-destructive">{{ form.errors.email }}</p>
                    </div>
                </CardContent>
            </Card>

            <!-- Code PIN -->
            <Card class="mx-4 mt-4 gap-0 py-4 shadow-none">
                <CardContent class="space-y-5 px-4 py-0">
                    <div class="flex items-center gap-1.5">
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
                                    class="h-14 w-14 rounded-2xl border border-border/50 bg-muted/30 text-center text-lg font-medium text-foreground outline-none focus:border-primary focus:ring-[3px] focus:ring-primary/20"
                                    @input="handlePinInput(currentPin, i, $event)"
                                    @keydown="handlePinKeydown(currentPin, i, $event)"
                                />
                            </div>
                            <button class="ml-auto flex h-12 w-12 items-center justify-center rounded-2xl border border-border/50 bg-muted/30" @click="showCurrentPin = !showCurrentPin">
                                <component :is="showCurrentPin ? EyeOff : Eye" class="h-4 w-4 text-muted-foreground/60" />
                            </button>
                        </div>
                        <p v-if="form.errors.current_pin" class="mt-1.5 text-xs text-red-500">{{ form.errors.current_pin }}</p>
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
                                    class="h-14 w-14 rounded-2xl border border-border/50 bg-muted/30 text-center text-lg font-medium text-foreground outline-none focus:border-primary focus:ring-[3px] focus:ring-primary/20"
                                    @input="handlePinInput(newPin, i, $event)"
                                    @keydown="handlePinKeydown(newPin, i, $event)"
                                />
                            </div>
                            <button class="ml-auto flex h-12 w-12 items-center justify-center rounded-2xl border border-border/50 bg-muted/30" @click="showNewPin = !showNewPin">
                                <component :is="showNewPin ? EyeOff : Eye" class="h-4 w-4 text-muted-foreground/60" />
                            </button>
                        </div>
                        <p v-if="form.errors.new_pin" class="mt-1.5 text-xs text-destructive">{{ form.errors.new_pin }}</p>
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
                                        'h-14 w-14 rounded-2xl text-center text-lg font-medium text-foreground outline-none focus:border-primary focus:ring-[3px] focus:ring-primary/20',
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
            <Card v-if="!user.has_google" class="mx-4 mt-4 gap-0 py-4 shadow-none">
                <CardContent class="space-y-5 px-4 py-0">
                    <span class="text-[11px] font-semibold uppercase tracking-widest text-muted-foreground/70">Mot de passe</span>

                    <div>
                        <label class="text-[11px] font-semibold uppercase tracking-widest text-foreground">Mot de passe actuel</label>
                        <div class="mt-1.5 flex items-center gap-2.5 rounded-2xl border border-border/50 bg-muted/30 px-3.5 py-2 transition-colors focus-within:border-primary focus-within:ring-[3px] focus-within:ring-primary/20">
                            <Lock class="h-4 w-4 text-amber-400" />
                            <input
                                v-model="form.current_password"
                                :type="showCurrentPassword ? 'text' : 'password'"
                                placeholder="••••••••••"
                                class="min-w-0 flex-1 border-none bg-transparent text-sm text-foreground shadow-none outline-none ring-0 placeholder:text-muted-foreground/50 focus:ring-0"
                            />
                            <button type="button" class="shrink-0" @click="showCurrentPassword = !showCurrentPassword">
                                <component :is="showCurrentPassword ? EyeOff : Eye" class="h-4 w-4 text-muted-foreground/50" />
                            </button>
                        </div>
                        <p v-if="form.errors.current_password" class="mt-1.5 text-xs text-destructive">{{ form.errors.current_password }}</p>
                    </div>

                    <div>
                        <label class="text-[11px] font-semibold uppercase tracking-widest text-foreground">Nouveau mot de passe</label>
                        <div class="mt-1.5 flex items-center gap-2.5 rounded-2xl border border-border/50 bg-muted/30 px-3.5 py-2 transition-colors focus-within:border-primary focus-within:ring-[3px] focus-within:ring-primary/20">
                            <Lock class="h-4 w-4 text-amber-400" />
                            <input
                                v-model="form.new_password"
                                :type="showNewPassword ? 'text' : 'password'"
                                placeholder="••••••••••••"
                                class="min-w-0 flex-1 border-none bg-transparent text-sm text-foreground shadow-none outline-none ring-0 placeholder:text-muted-foreground/50 focus:ring-0"
                            />
                            <button type="button" class="shrink-0" @click="showNewPassword = !showNewPassword">
                                <component :is="showNewPassword ? EyeOff : Eye" class="h-4 w-4 text-muted-foreground/50" />
                            </button>
                        </div>
                        <p v-if="form.errors.new_password" class="mt-1.5 text-xs text-destructive">{{ form.errors.new_password }}</p>

                        <!-- Jauge de force du mot de passe -->
                        <div v-if="form.new_password" class="mt-2 space-y-1">
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

                    <div>
                        <label class="text-[11px] font-semibold uppercase tracking-widest text-foreground">Confirmer le mot de passe</label>
                        <div
                            :class="[
                                'mt-1.5 flex items-center gap-2.5 rounded-2xl px-3.5 py-2 transition-colors focus-within:border-primary focus-within:ring-[3px] focus-within:ring-primary/20',
                                passwordMismatch ? 'border border-red-400 bg-red-50' : 'border border-border/50 bg-muted/30',
                            ]"
                        >
                            <Lock class="h-4 w-4 text-amber-400" />
                            <input
                                v-model="form.new_password_confirmation"
                                :type="showConfirmPassword ? 'text' : 'password'"
                                placeholder="••••••••••••"
                                class="min-w-0 flex-1 border-none bg-transparent text-sm text-foreground shadow-none outline-none ring-0 placeholder:text-muted-foreground/50 focus:ring-0"
                            />
                            <button type="button" class="shrink-0" @click="showConfirmPassword = !showConfirmPassword">
                                <component :is="showConfirmPassword ? EyeOff : Eye" class="h-4 w-4 text-muted-foreground/50" />
                            </button>
                        </div>
                        <p v-if="passwordMismatch" class="mt-1.5 text-xs text-red-500">Les mots de passe ne correspondent pas</p>
                    </div>

                </CardContent>
            </Card>

            <div class="mx-4 mt-5 mb-4">
                <button
                    class="w-full rounded-2xl bg-primary py-3.5 text-sm font-semibold text-white transition-opacity disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="form.processing || saveDisabled"
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
