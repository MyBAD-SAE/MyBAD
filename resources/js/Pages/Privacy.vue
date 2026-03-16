<script setup>
import { Head } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

function goBack() {
    window.history.back();
}
</script>

<template>
    <Head title="Politique de confidentialité" />

    <div class="min-h-screen bg-background">
        <div class="mx-auto max-w-2xl px-6 py-10">
            <button @click="goBack" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="h-4 w-4" />
                Retour
            </button>

            <div class="mt-6 flex items-center gap-3">
                <img src="/images/logo_mybad.png" alt="MyBAD" class="h-10 w-10 rounded-md" />
                <h1 class="text-xl font-bold">Politique de Confidentialité</h1>
            </div>

            <p class="mt-2 text-sm text-muted-foreground">Dernière mise à jour : 16 mars 2026</p>

            <div class="mt-8 space-y-8 text-sm leading-relaxed text-foreground">
                <section>
                    <h2 class="text-base font-semibold">1. Responsable du traitement</h2>
                    <p class="mt-2 text-muted-foreground">
                        Le responsable du traitement des données personnelles collectées via l'application MyBAD est l'équipe MyBAD. Pour toute question, vous pouvez nous contacter à l'adresse : contact@mybad.fr
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-semibold">2. Données collectées</h2>
                    <p class="mt-2 text-muted-foreground">
                        Dans le cadre de l'utilisation de MyBAD, nous collectons les données suivantes :
                    </p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-muted-foreground">
                        <li><strong class="font-medium text-foreground">Données d'identification</strong> : nom, prénom, adresse email</li>
                        <li><strong class="font-medium text-foreground">Données de connexion</strong> : mot de passe (hashé), adresse IP, données de session</li>
                        <li><strong class="font-medium text-foreground">Données d'utilisation</strong> : scores de matchs, classement ELO, historique des performances</li>
                        <li><strong class="font-medium text-foreground">Photo de profil</strong> : si l'utilisateur choisit d'en ajouter une</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-base font-semibold">3. Finalités du traitement</h2>
                    <p class="mt-2 text-muted-foreground">Les données collectées sont utilisées pour :</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-muted-foreground">
                        <li>Créer et gérer les comptes utilisateurs</li>
                        <li>Permettre l'authentification et la sécurisation de l'accès</li>
                        <li>Enregistrer les résultats de matchs et calculer le classement ELO</li>
                        <li>Afficher les statistiques et l'historique de progression</li>
                        <li>Permettre aux administrateurs de gérer leurs classes</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-base font-semibold">4. Base légale</h2>
                    <p class="mt-2 text-muted-foreground">
                        Le traitement des données repose sur le consentement de l'utilisateur lors de la création de son compte (article 6.1.a du RGPD) ainsi que sur l'exécution du contrat d'utilisation du service (article 6.1.b du RGPD).
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-semibold">5. Durée de conservation</h2>
                    <p class="mt-2 text-muted-foreground">
                        Les données personnelles sont conservées pendant toute la durée d'utilisation du compte. En cas de suppression du compte, les données sont effacées dans un délai de 30 jours. Les données de classement peuvent être conservées sous forme anonymisée à des fins statistiques.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-semibold">6. Partage des données</h2>
                    <p class="mt-2 text-muted-foreground">
                        Les données personnelles ne sont pas vendues ni transmises à des tiers à des fins commerciales. Les données suivantes peuvent être visibles par d'autres utilisateurs :
                    </p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-muted-foreground">
                        <li>Prénom et nom (dans le classement de la classe)</li>
                        <li>Classement ELO et résultats de matchs (aux membres de la même classe)</li>
                        <li>Classement via les liens publics (si activé par l'administrateur)</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-base font-semibold">7. Sécurité des données</h2>
                    <p class="mt-2 text-muted-foreground">
                        Nous mettons en œuvre les mesures techniques et organisationnelles appropriées pour protéger vos données : chiffrement des mots de passe (bcrypt), connexions sécurisées (HTTPS), limitation des accès aux données selon les rôles (joueur, administrateur).
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-semibold">8. Vos droits (RGPD)</h2>
                    <p class="mt-2 text-muted-foreground">Conformément au Règlement Général sur la Protection des Données, vous disposez des droits suivants :</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-muted-foreground">
                        <li><strong class="font-medium text-foreground">Droit d'accès</strong> : obtenir une copie de vos données personnelles</li>
                        <li><strong class="font-medium text-foreground">Droit de rectification</strong> : corriger des données inexactes ou incomplètes</li>
                        <li><strong class="font-medium text-foreground">Droit à l'effacement</strong> : demander la suppression de vos données</li>
                        <li><strong class="font-medium text-foreground">Droit à la portabilité</strong> : recevoir vos données dans un format structuré</li>
                        <li><strong class="font-medium text-foreground">Droit d'opposition</strong> : vous opposer au traitement de vos données</li>
                    </ul>
                    <p class="mt-2 text-muted-foreground">
                        Pour exercer ces droits, contactez-nous à : contact@mybad.fr
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-semibold">9. Cookies</h2>
                    <p class="mt-2 text-muted-foreground">
                        MyBAD utilise uniquement des cookies techniques nécessaires au fonctionnement de l'application (session d'authentification, token CSRF). Aucun cookie de traçage ou publicitaire n'est utilisé.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-semibold">10. Modification de la politique</h2>
                    <p class="mt-2 text-muted-foreground">
                        Nous nous réservons le droit de modifier cette politique de confidentialité. En cas de modification substantielle, les utilisateurs seront informés via l'application. La date de dernière mise à jour est indiquée en haut de cette page.
                    </p>
                </section>
            </div>
        </div>
    </div>
</template>
