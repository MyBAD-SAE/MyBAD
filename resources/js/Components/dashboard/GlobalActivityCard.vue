<script setup>
import { Card, CardContent } from '@/Components/ui/card';
import { Flame, Target } from 'lucide-vue-next';

defineProps({
    winStreak: { type: Number, default: 0 },
    totalMatches: { type: Number, default: 0 },
});
</script>

<template>
    <div>
        <h3 class="text-lg font-semibold text-foreground">Activité globale</h3>
<!--        <p class="text-sm text-muted-foreground">Depuis le début</p>-->

        <Card class="mt-3 shadow-none">
            <CardContent class="grid grid-cols-2 gap-3 p-3">
                <div class="flex flex-col items-center rounded-xl bg-orange-50 p-4">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-orange-100">
                        <Flame class="h-5 w-5 text-orange-500" />
                    </div>
                    <span class="mt-2 text-2xl font-semibold text-foreground">{{ winStreak }}</span>
                    <span class="text-xs text-muted-foreground">Win streak</span>
                </div>

                <div class="flex flex-col items-center rounded-xl bg-primary/5 p-4">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10">
                        <Target class="h-5 w-5 text-primary" />
                    </div>
                    <span class="mt-2 text-2xl font-semibold text-foreground">{{ totalMatches }}</span>
                    <span class="text-xs text-muted-foreground">Matchs joués</span>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
