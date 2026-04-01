<script setup>
import { ref } from 'vue';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetDescription,
    SheetTrigger,
} from '@/Components/ui/sheet/index.ts';
import { CalendarDays, ChevronDown } from 'lucide-vue-next';

const props = defineProps({
    sessions: {
        type: Array,
        default: () => [
            { id: 1, day: 'Mardi', time: '18h30 – 20h30' },
            { id: 2, day: 'Jeudi', time: '18h30 – 20h30' },
        ],
    },
    modelValue: { type: String, default: 'Mardi' },
});

const emit = defineEmits(['update:modelValue']);

const open = ref(false);

const select = (session) => {
    emit('update:modelValue', session.day);
    open.value = false;
};
</script>

<template>
    <Sheet v-model:open="open">
        <SheetTrigger as-child>
            <slot name="trigger">
                <button class="inline-flex items-center gap-1.5 rounded-full border border-primary/30 bg-primary/5 px-3 py-1 text-sm font-medium text-primary">
                    <CalendarDays class="h-3.5 w-3.5" />
                    Séance du {{ modelValue.toLowerCase() }}
                    <ChevronDown class="h-3.5 w-3.5" />
                </button>
            </slot>
        </SheetTrigger>

        <SheetContent side="bottom" class="rounded-t-2xl px-5 pb-8 gap-0">
            <SheetHeader class="text-left">
                <div class="flex items-center gap-2">
                    <CalendarDays class="h-5 w-5 text-primary" />
                    <SheetTitle class="text-lg font-bold">Séance du jour</SheetTitle>
                </div>
                <SheetDescription>Choisis le jour de ta séance habituelle</SheetDescription>
            </SheetHeader>

            <div class="mt-4 space-y-3">
                <button
                    v-for="session in sessions"
                    :key="session.id"
                    class="w-full rounded-xl border p-4 text-left transition-colors"
                    :class="modelValue === session.day ? 'border-primary bg-primary/5' : 'border-border'"
                    @click="select(session)"
                >
                    <p class="font-semibold text-foreground">{{ session.day }}</p>
                    <p class="mt-0.5 text-sm text-muted-foreground">{{ session.time }}</p>
                </button>
            </div>
        </SheetContent>
    </Sheet>
</template>
