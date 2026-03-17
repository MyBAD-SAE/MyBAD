<script setup>
import { Head } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

function goBack() {
    window.history.back();
}
</script>

<template>
    <Head title="Conditions d'utilisation" />

    <div class="min-h-screen bg-background">
        <div class="mx-auto max-w-2xl px-6 py-10">
            <button @click="goBack" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="h-4 w-4" />
                Retour
            </button>

            <div class="mt-6 flex items-center gap-3">
                <img src="/images/logo_mybad.png" alt="MyBAD" class="h-10 w-10 rounded-md" />
                <h1 class="text-xl font-bold">Conditions Générales d'Utilisation</h1>
            </div>

            <p class="mt-2 text-sm text-muted-foreground">Dernière mise à jour : 16 mars 2026</p>

            <div class="mt-8 space-y-8 text-sm leading-relaxed text-foreground">
                <section>
                    <h2 class="text-base font-semibold">1. Objet</h2>
                    <p class="mt-2 text-muted-foreground">
                        Les présentes Conditions Générales d'Utilisation (ci-après « CGU ») ont pour objet de définir les modalités d'accès et d'utilisation de l'application MyBAD, une plateforme de gestion des scores et du classement ELO pour les cours de badminton.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-semibold">2. Acceptation des CGU</h2>
                    <p class="mt-2 text-muted-foreground">
                        L'utilisation de l'application MyBAD implique l'acceptation pleine et entière des présentes CGU. En créant un compte, l'utilisateur reconnaît avoir pris connaissance de ces conditions et les accepter sans réserve.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-semibold">3. Description du service</h2>
                    <p class="mt-2 text-muted-foreground">
                        MyBAD est une application permettant de :
                    </p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-muted-foreground">
                        <li>Gérer des classes et des sessions de badminton</li>
                        <li>Enregistrer les scores des matchs entre joueurs</li>
                        <li>Calculer et suivre le classement ELO des joueurs</li>
                        <li>Consulter les statistiques et l'historique des performances</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-base font-semibold">4. Inscription et compte utilisateur</h2>
                    <p class="mt-2 text-muted-foreground">
                        Pour accéder aux fonctionnalités de l'application, l'utilisateur doit créer un compte en fournissant des informations exactes et à jour (nom, prénom, adresse email). L'utilisateur est responsable de la confidentialité de ses identifiants de connexion.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-semibold">5. Données personnelles</h2>
                    <p class="mt-2 text-muted-foreground">
                        Les données collectées par MyBAD sont limitées aux informations nécessaires au fonctionnement du service : nom, prénom, adresse email, scores de matchs et classement ELO. Ces données sont stockées de manière sécurisée et ne sont pas transmises à des tiers. Conformément au RGPD, l'utilisateur dispose d'un droit d'accès, de rectification et de suppression de ses données personnelles.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-semibold">6. Responsabilités de l'utilisateur</h2>
                    <p class="mt-2 text-muted-foreground">L'utilisateur s'engage à :</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-muted-foreground">
                        <li>Utiliser l'application conformément à son objet</li>
                        <li>Ne pas saisir de faux scores ou manipuler le classement</li>
                        <li>Respecter les autres utilisateurs de la plateforme</li>
                        <li>Ne pas tenter de compromettre la sécurité de l'application</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-base font-semibold">7. Propriété intellectuelle</h2>
                    <p class="mt-2 text-muted-foreground">
                        L'ensemble des éléments composant l'application MyBAD (design, logo, code source, textes) sont protégés par le droit de la propriété intellectuelle. Toute reproduction ou utilisation non autorisée est interdite.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-semibold">8. Modification des CGU</h2>
                    <p class="mt-2 text-muted-foreground">
                        MyBAD se réserve le droit de modifier les présentes CGU à tout moment. Les utilisateurs seront informés de toute modification et l'utilisation continue de l'application vaut acceptation des nouvelles conditions.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-semibold">9. Contact</h2>
                    <p class="mt-2 text-muted-foreground">
                        Pour toute question relative aux présentes CGU, vous pouvez nous contacter à l'adresse suivante : contact@mybad.fr
                    </p>
                </section>
            </div>
        </div>
    </div>
</template>
