<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="MyBAD",
 *     description="Application de gestion de tournois de badminton en milieu scolaire. Développée avec Laravel 12, Vue 3 et Inertia.js. Les routes Inertia retournent leurs props en JSON lors des requêtes XHR (header X-Inertia: true).",
 *     @OA\Contact(name="Maël Sellier", email="selliermael@gmail.com")
 * )
 * @OA\Server(url="http://www.my-bad.local", description="Serveur local Laragon")
 * @OA\SecurityScheme(
 *     securityScheme="session",
 *     type="apiKey",
 *     in="cookie",
 *     name="laravel_session",
 *     description="Session Laravel (cookie HTTP-only)"
 * )
 * @OA\Tag(name="Auth Admin", description="Authentification des administrateurs")
 * @OA\Tag(name="Admin - Tableau de bord", description="Tableau de bord et sélection de cours")
 * @OA\Tag(name="Admin - Joueurs", description="Gestion des joueurs d'un cours")
 * @OA\Tag(name="Admin - Matchs", description="Consultation et modification des matchs")
 * @OA\Tag(name="Admin - Classement", description="Classement ELO de la classe")
 * @OA\Tag(name="Admin - Règles", description="Paramètres ELO et règles des défis")
 * @OA\Tag(name="Admin - Séance", description="Démarrage et clôture d'une séance")
 * @OA\Tag(name="Admin - Compte", description="Gestion du profil administrateur")
 * @OA\Tag(name="Admin - Administrateurs", description="Gestion des co-administrateurs")
 * @OA\Tag(name="Auth Joueur", description="Authentification des joueurs")
 * @OA\Tag(name="Joueur - Tableau de bord", description="Tableau de bord joueur")
 * @OA\Tag(name="Joueur - Matchs", description="Déclaration et historique des matchs")
 * @OA\Tag(name="Joueur - Classement", description="Classement ELO et historique")
 * @OA\Tag(name="Joueur - Compte", description="Gestion du profil joueur")
 */
class SwaggerInfo {}
