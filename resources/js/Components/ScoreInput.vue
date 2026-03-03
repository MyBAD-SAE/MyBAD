<script setup>
defineProps({
    label: String,
    modelValue: [Number, String],
    error: String,
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <label v-if="label" class="block text-xs font-medium text-gray-600 mb-1">{{ label }}</label>
        <input
            type="number"
            min="0"
            max="30"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value ? parseInt($event.target.value) : null)"
            class="w-20 rounded-md border-gray-300 text-center shadow-sm focus:border-primary-500 focus:ring-primary-500"
        />
        <p v-if="error" class="mt-1 text-xs text-red-600">{{ error }}</p>
    </div>
</template>
