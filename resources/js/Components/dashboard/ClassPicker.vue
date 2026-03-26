<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetDescription,
    SheetTrigger,
} from '@/Components/ui/sheet';
import { GraduationCap, ChevronDown, Check } from 'lucide-vue-next';

const props = defineProps({
    classes: { type: Array, default: () => [] },
    selectedClassId: { type: Number, default: null },
});

const open = ref(false);

const form = useForm({ class_id: null });

const selectedClass = () => props.classes.find(c => c.id === props.selectedClassId);

function select(cls) {
    if (cls.id === props.selectedClassId) {
        open.value = false;
        return;
    }
    form.class_id = cls.id;
    form.post(route('player.class.select'), {
        preserveScroll: true,
        onSuccess: () => { open.value = false; },
    });
}
</script>

<template>
    <Sheet v-model:open="open">
        <SheetTrigger as-child>
            <slot name="trigger">
                <button class="ml-auto inline-flex items-center gap-1.5 rounded-full border border-primary/30 bg-primary/5 px-3 py-1 text-sm font-medium text-primary max-w-[200px]">
                    <GraduationCap class="h-3.5 w-3.5 shrink-0" />
                    <span class="truncate">{{ selectedClass()?.name ?? 'Choisir un cours' }}</span>
                    <ChevronDown class="h-3.5 w-3.5 shrink-0" />
                </button>
            </slot>
        </SheetTrigger>

        <SheetContent side="bottom" class="rounded-t-2xl px-5 pb-8 gap-0">
            <SheetHeader class="text-left">
                <div class="flex items-center gap-2">
                    <GraduationCap class="h-5 w-5 text-primary" />
                    <SheetTitle class="text-lg font-bold">Choisir un cours</SheetTitle>
                </div>
                <SheetDescription>Sélectionne le cours dont tu veux voir les données</SheetDescription>
            </SheetHeader>

            <div class="mt-4 space-y-3">
                <button
                    v-for="cls in classes"
                    :key="cls.id"
                    class="flex w-full items-center justify-between rounded-xl border p-4 text-left transition-colors"
                    :class="cls.id === selectedClassId ? 'border-primary bg-primary/5' : 'border-border'"
                    :disabled="form.processing"
                    @click="select(cls)"
                >
                    <p class="font-semibold text-foreground">{{ cls.name }}</p>
                    <Check v-if="cls.id === selectedClassId" class="h-4 w-4 text-primary" />
                </button>
            </div>
        </SheetContent>
    </Sheet>
</template>
