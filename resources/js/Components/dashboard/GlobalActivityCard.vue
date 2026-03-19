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
        <h3 class="text-lg font-bold text-foreground">Activité globale</h3>
        <p class="text-sm text-muted-foreground">Depuis le début</p>

        <div class="mt-3 grid grid-cols-2 gap-3">
            <Card class="bg-orange-50 border-0 shadow-none">
                <CardContent class="flex flex-col items-center p-4">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-orange-100">
                        <Flame class="h-5 w-5 text-orange-500" />
                    </div>
                    <span class="mt-2 text-2xl font-bold text-foreground">{{ winStreak }}</span>
                    <span class="text-xs text-muted-foreground">Win streak</span>
                </CardContent>
            </Card>

            <Card class="bg-primary/5 border-0 shadow-none">
                <CardContent class="flex flex-col items-center p-4">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10">
                        <Target class="h-5 w-5 text-primary" />
                    </div>
                    <span class="mt-2 text-2xl font-bold text-foreground">{{ totalMatches }}</span>
                    <span class="text-xs text-muted-foreground">Matchs joués</span>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
