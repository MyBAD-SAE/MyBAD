<script setup>
import { LayoutDashboard, Swords, Trophy, User, Plus } from 'lucide-vue-next';

defineProps({
    active: { type: String, default: 'dashboard' },
});

const navItems = [
    { key: 'dashboard', label: 'Dashboard', icon: LayoutDashboard },
    { key: 'matchs', label: 'Matchs', icon: Swords },
    { key: 'add', label: '', icon: Plus },
    { key: 'classement', label: 'Classement', icon: Trophy },
    { key: 'profil', label: 'Profil', icon: User },
];
</script>

<template>
    <nav class="fixed bottom-0 left-0 right-0 z-50 border-t bg-background sm:hidden">
        <div class="flex items-center justify-around py-2">
            <button
                v-for="item in navItems"
                :key="item.key"
                class="flex flex-col items-center gap-0.5"
                :class="item.key === 'add' ? '' : 'min-w-[60px]'"
            >
                <!-- Center add button -->
                <template v-if="item.key === 'add'">
                    <div class="flex h-12 w-12 -mt-5 items-center justify-center rounded-full bg-primary text-white shadow-lg">
                        <Plus class="h-6 w-6" />
                    </div>
                </template>

                <template v-else>
                    <component
                        :is="item.icon"
                        class="h-5 w-5"
                        :class="active === item.key ? 'text-primary' : 'text-muted-foreground'"
                    />
                    <span
                        class="text-[10px] font-medium"
                        :class="active === item.key ? 'text-primary' : 'text-muted-foreground'"
                    >
                        {{ item.label }}
                    </span>
                </template>
            </button>
        </div>
    </nav>
</template>
