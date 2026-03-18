<script setup>
import { Card, CardContent } from '@/Components/ui/card';

defineProps({
    total: { type: Number, default: 0 },
    wins: { type: Number, default: 0 },
    losses: { type: Number, default: 0 },
});
</script>

<template>
    <Card>
        <CardContent class="p-4">
            <span class="text-sm font-medium text-muted-foreground">Matchs</span>
            <div class="mt-1 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-foreground">{{ total }}</span>
                <span class="text-sm font-medium text-primary">{{ wins }}V / {{ losses }}D</span>
            </div>
            <!-- Mini bar chart -->
            <div class="mt-4 flex items-end gap-2 h-12">
                <div class="flex-1 rounded bg-muted" :style="{ height: '40%' }" />
                <div class="flex-1 rounded bg-muted" :style="{ height: '70%' }" />
                <div class="flex-1 rounded bg-muted" :style="{ height: '55%' }" />
                <div class="flex-1 rounded bg-muted" :style="{ height: '85%' }" />
                <div class="flex-1 rounded bg-muted" :style="{ height: '60%' }" />
            </div>
        </CardContent>
    </Card>
</template>
