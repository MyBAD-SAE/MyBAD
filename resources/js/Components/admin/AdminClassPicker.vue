<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { ChevronDown, Check } from 'lucide-vue-next';

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
    form.post(route('admin.class.select'), {
        preserveScroll: true,
        onSuccess: () => { open.value = false; },
    });
}

function close(e) {
    if (!e.target.closest('.class-picker-container')) {
        open.value = false;
    }
}
</script>

<template>
    <div class="class-picker-container relative" v-if="classes.length > 1" @focusout="close">
        <button
            class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-4 py-2 text-sm font-medium text-foreground transition-colors hover:bg-gray-200"
            @click="open = !open"
        >
            <span class="h-2 w-2 rounded-full bg-primary" />
            {{ selectedClass()?.name ?? 'Choisir un cours' }}
            <ChevronDown class="h-4 w-4 text-muted-foreground transition-transform" :class="open && 'rotate-180'" />
        </button>

        <div
            v-if="open"
            class="absolute right-0 top-full z-50 mt-2 min-w-[200px] rounded-xl border border-border bg-white py-1 shadow-lg"
            @click.stop
        >
            <button
                v-for="cls in classes"
                :key="cls.id"
                class="flex w-full items-center justify-between px-4 py-2.5 text-left text-sm transition-colors hover:bg-gray-50"
                :class="cls.id === selectedClassId && 'text-primary font-medium'"
                :disabled="form.processing"
                @click="select(cls)"
            >
                <span>{{ cls.name }}</span>
                <Check v-if="cls.id === selectedClassId" class="h-4 w-4 text-primary" />
            </button>
        </div>
    </div>

    <div v-else-if="classes.length === 1" class="inline-flex items-center gap-2 rounded-full border border-border bg-white px-4 py-2 text-sm font-medium text-foreground">
        <span class="h-2 w-2 rounded-full bg-primary" />
        {{ classes[0].name }}
    </div>
</template>
