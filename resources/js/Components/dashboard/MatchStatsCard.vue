<script setup>
import { computed } from 'vue';
import { Card, CardContent } from '@/Components/ui/card';

const props = defineProps({
    total: { type: Number, default: 0 },
    wins: { type: Number, default: 0 },
    losses: { type: Number, default: 0 },
    sessions: { type: Array, default: () => [] },
});

const maxPerSession = computed(() => {
    if (props.sessions.length === 0) return 1;
    return Math.max(...props.sessions.map(s => s.wins + s.losses), 1);
});
</script>

<template>
    <Card class="shadow-none">
        <CardContent class="flex flex-col p-4 h-full">
            <span class="text-sm font-bold text-foreground">Matchs</span>
            <div class="mt-1 flex items-baseline gap-2">
                <span class="text-3xl font-semibold text-foreground">{{ total }}</span>
                <span class="text-sm font-medium text-primary">{{ wins }}V / {{ losses }}D</span>
            </div>
            <div class="mt-4 flex flex-1 items-end gap-1.5">
                <div
                    v-for="(session, i) in sessions"
                    :key="i"
                    class="flex flex-1 flex-col items-center gap-0"
                    :style="{ height: '100%' }"
                >
                    <div
                        class="flex w-full flex-1 flex-col items-stretch justify-end gap-[1px]"
                    >
                        <div
                            class="flex flex-col items-stretch gap-[1px]"
                            :style="{ height: ((session.wins + session.losses) / maxPerSession * 100) + '%' }"
                        >
                            <div
                                v-if="session.wins > 0"
                                class="rounded-t bg-primary"
                                :style="{ flex: session.wins }"
                            />
                            <div
                                v-if="session.losses > 0"
                                class="rounded-b bg-destructive/60"
                                :style="{ flex: session.losses }"
                            />
                        </div>
                    </div>
                    <span class="mt-1 text-[9px] text-muted-foreground">{{ session.date }}</span>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
