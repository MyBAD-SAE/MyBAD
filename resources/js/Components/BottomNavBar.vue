<script setup>
import { LayoutDashboard, Swords, Trophy, User, Plus } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

defineProps({
    active: { type: String, default: 'dashboard' },
});

const navItems = [
    { key: 'dashboard', label: 'Dashboard', icon: LayoutDashboard, route: 'home' },
    { key: 'matchs', label: 'Matchs', icon: Swords, route: 'matchs' },
    { key: 'add', label: '', icon: Plus, route: null },
    { key: 'classement', label: 'Classement', icon: Trophy, route: 'classements' },
    { key: 'profil', label: 'Profil', icon: User, route: 'player.account.index' },
];
</script>

<template>
    <nav class="fixed bottom-0 left-0 right-0 z-50 border-t bg-white sm:hidden">
        <div class="flex items-center justify-around py-2">
            <template v-for="item in navItems" :key="item.key">
                <!-- Center add button -->
                <button
                    v-if="item.key === 'add'"
                    class="flex flex-col items-center gap-0.5"
                >
                    <div class="flex h-12 w-12 -mt-5 items-center justify-center rounded-full bg-primary text-white shadow-lg">
                        <Plus class="h-6 w-6" />
                    </div>
                </button>

                <Link
                    v-else
                    :href="route(item.route)"
                    class="flex flex-col items-center gap-0.5 min-w-[60px]"
                >
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
                </Link>
            </template>
        </div>
    </nav>
</template>
