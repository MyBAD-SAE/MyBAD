<script setup>
import { ref, computed, nextTick } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Lock, ShieldCheck } from 'lucide-vue-next';

defineProps({
    modelValue: { type: Boolean, default: false },
});

const digits = ref(['', '', '', '']);
const inputs = ref([]);

const pin = computed(() => digits.value.join(''));
const isComplete = computed(() => digits.value.every(d => d !== ''));

const form = useForm({ pin: '' });

function onInput(index) {
    // Only keep last digit
    digits.value[index] = digits.value[index].replace(/\D/g, '').slice(-1);

    // Auto-advance
    if (digits.value[index] && index < 3) {
        nextTick(() => inputs.value[index + 1]?.focus());
    }
}

function onKeydown(event, index) {
    if (event.key === 'Backspace' && !digits.value[index] && index > 0) {
        nextTick(() => inputs.value[index - 1]?.focus());
    }
}

function onPaste(event) {
    event.preventDefault();
    const pasted = event.clipboardData.getData('text').replace(/\D/g, '').slice(0, 4);
    for (let i = 0; i < 4; i++) {
        digits.value[i] = pasted[i] || '';
    }
    const focusIndex = Math.min(pasted.length, 3);
    nextTick(() => inputs.value[focusIndex]?.focus());
}

function submit() {
    form.pin = pin.value;
    form.post(route('player.pin.store'));
}
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center sm:hidden">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" />

                <!-- Modal -->
                <Transition
                    enter-active-class="transition duration-300 ease-out delay-100"
                    enter-from-class="opacity-0 translate-y-6 scale-95"
                    enter-to-class="opacity-100 translate-y-0 scale-100"
                >
                    <div v-if="modelValue" class="relative mx-6 w-full max-w-sm rounded-3xl bg-white px-7 py-9 shadow-2xl">
                        <!-- Icon -->
                        <div class="flex justify-center">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary/10">
                                <Lock class="h-7 w-7 text-primary" />
                            </div>
                        </div>

                        <!-- Title -->
                        <h2 class="mt-5 text-center text-xl font-bold text-foreground">
                            Créer votre code PIN
                        </h2>
                        <p class="mt-2 text-center text-sm leading-relaxed text-muted-foreground">
                            Ce code à 4 chiffres vous permettra de valider vos matchs.
                        </p>

                        <!-- PIN inputs -->
                        <div class="mt-7 flex justify-center gap-3">
                            <input
                                v-for="(_, i) in 4"
                                :key="i"
                                :ref="el => inputs[i] = el"
                                v-model="digits[i]"
                                type="text"
                                inputmode="numeric"
                                maxlength="1"
                                class="h-14 w-14 rounded-2xl border-2 bg-muted/30 text-center text-2xl font-bold text-foreground outline-none transition-all duration-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10"
                                :class="digits[i] ? 'border-primary/40' : 'border-border'"
                                @input="onInput(i)"
                                @keydown="onKeydown($event, i)"
                                @paste="onPaste"
                            />
                        </div>

                        <!-- Error -->
                        <p v-if="form.errors.pin" class="mt-3 text-center text-xs text-red-500">
                            {{ form.errors.pin }}
                        </p>

                        <!-- Button -->
                        <button
                            class="mt-7 flex w-full items-center justify-center gap-2 rounded-2xl py-3.5 text-sm font-semibold text-white transition-all duration-200"
                            :class="isComplete && !form.processing
                                ? 'bg-primary active:scale-[0.98]'
                                : 'bg-muted-foreground/20 cursor-not-allowed'"
                            :disabled="!isComplete || form.processing"
                            @click="submit"
                        >
                            <ShieldCheck v-if="!form.processing" class="h-4 w-4" />
                            {{ form.processing ? 'Enregistrement...' : 'Confirmer' }}
                        </button>

                        <!-- Hint -->
                        <p class="mt-4 text-center text-xs text-muted-foreground/60">
                            Vous pourrez modifier ce code plus tard dans vos paramètres.
                        </p>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
