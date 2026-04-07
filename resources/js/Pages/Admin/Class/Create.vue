<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Input } from '@/Components/ui/input';
import { Button } from '@/Components/ui/button';
import { Label } from '@/Components/ui/label';
import { ArrowLeft, GraduationCap } from 'lucide-vue-next';

const form = useForm({
    school_year: '',
    name: '',
    address: '',
    description: '',
});

const submit = () => {
    form.post(route('admin.class.store'));
};
</script>

<template>
    <Head title="Nouveau cours - Admin" />

    <AdminLayout>
        <div class="p-8">
            <Link
                :href="route('admin.dashboard')"
                class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground transition-colors mb-6"
            >
                <ArrowLeft class="h-4 w-4" />
                Retour au dashboard
            </Link>

            <div class="mx-auto max-w-lg">
                <!-- Header -->
                <div class="mb-8 flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10">
                        <GraduationCap class="h-7 w-7 text-primary" />
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-foreground">Nouveau cours</h1>
                        <p class="text-sm text-muted-foreground">Renseignez les informations du cours</p>
                    </div>
                </div>

                <!-- Form -->
                <div class="rounded-2xl border border-border p-6 space-y-5">
                    <!-- Année scolaire -->
                    <div>
                        <Label for="school_year" class="mb-2 block text-sm font-medium text-foreground">
                            Année scolaire
                        </Label>
                        <Input
                            id="school_year"
                            v-model="form.school_year"
                            type="text"
                            placeholder="2025/2026"
                            class="h-11 rounded-xl shadow-none focus-visible:ring-1 focus-visible:ring-primary"
                        />
                        <p v-if="form.errors.school_year" class="mt-1.5 text-sm text-destructive">
                            {{ form.errors.school_year }}
                        </p>
                    </div>

                    <!-- Nom -->
                    <div>
                        <Label for="name" class="mb-2 block text-sm font-medium text-foreground">
                            Nom du cours
                        </Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            placeholder="Badminton L3 S2"
                            class="h-11 rounded-xl shadow-none focus-visible:ring-1 focus-visible:ring-primary"
                        />
                        <p v-if="form.errors.name" class="mt-1.5 text-sm text-destructive">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Lieu -->
                    <div>
                        <Label for="address" class="mb-2 block text-sm font-medium text-foreground">
                            Lieu <span class="text-muted-foreground/60 font-normal">(optionnel)</span>
                        </Label>
                        <Input
                            id="address"
                            v-model="form.address"
                            type="text"
                            placeholder="Gymnase universitaire, Bât. B"
                            class="h-11 rounded-xl shadow-none focus-visible:ring-1 focus-visible:ring-primary"
                        />
                        <p v-if="form.errors.address" class="mt-1.5 text-sm text-destructive">
                            {{ form.errors.address }}
                        </p>
                    </div>

                    <!-- Description -->
                    <div>
                        <Label for="description" class="mb-2 block text-sm font-medium text-foreground">
                            Description <span class="text-muted-foreground/60 font-normal">(optionnel)</span>
                        </Label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            placeholder="Informations complémentaires sur le cours..."
                            rows="3"
                            class="flex w-full rounded-xl border border-input bg-transparent px-3 py-2 text-sm shadow-none outline-none placeholder:text-muted-foreground focus:ring-1 focus:ring-primary resize-none"
                        />
                        <p v-if="form.errors.description" class="mt-1.5 text-sm text-destructive">
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 pt-1">
                        <Button
                            variant="outline"
                            size="lg"
                            class="flex-1 rounded-xl"
                            as-child
                        >
                            <Link :href="route('admin.dashboard')">Annuler</Link>
                        </Button>
                        <Button
                            size="lg"
                            class="flex-1 rounded-xl"
                            :disabled="form.processing || !form.school_year.trim() || !form.name.trim()"
                            @click="submit"
                        >
                            Créer le cours
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
