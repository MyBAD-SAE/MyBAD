<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Création des Utilisateurs (1 Admin, 10 Joueurs)
        $users = [];

        // --- Admin ---
        $adminId = Str::uuid()->toString();
        $users[] = [
            'id' => $adminId,
            'first_name' => 'Admin',
            'last_name' => 'MyBAD',
            'email' => 'admin@mybad.test',
            'password' => Hash::make('password'),
            'profile_picture' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRUnRDZVtztZF0WEWoye99mHe-3E6iGqNPmsw&s",
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // --- Joueurs ---
        $playerIds = [];

        $id1 = Str::uuid()->toString();
        $playerIds[] = $id1;
        $users[] = [
            'id' => $id1,
            'first_name' => 'Lucas',
            'last_name' => 'Torres',
            'email' => 'lucas.torres@mybad.test',
            'password' => Hash::make('password'),
            'profile_picture' => 'https://randomuser.me/api/portraits/men/32.jpg',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $id2 = Str::uuid()->toString();
        $playerIds[] = $id2;
        $users[] = [
            'id' => $id2,
            'first_name' => 'Victor',
            'last_name' => 'Roué',
            'email' => 'victor.roue@mybad.test',
            'password' => Hash::make('password'),
            'profile_picture' => 'https://randomuser.me/api/portraits/men/44.jpg',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $id3 = Str::uuid()->toString();
        $playerIds[] = $id3;
        $users[] = [
            'id' => $id3,
            'first_name' => 'Antoine',
            'last_name' => 'Bernard',
            'email' => 'antoine.bernard@mybad.test',
            'password' => Hash::make('password'),
            'profile_picture' => 'https://randomuser.me/api/portraits/men/12.jpg',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $id4 = Str::uuid()->toString();
        $playerIds[] = $id4;
        $users[] = [
            'id' => $id4,
            'first_name' => 'Serge',
            'last_name' => 'Lama',
            'email' => 'serge.lama@mybad.test',
            'password' => Hash::make('password'),
            'profile_picture' => 'https://npr.brightspotcdn.com/dims3/default/strip/false/crop/2901x1632+0+0/resize/1100/quality/50/format/jpeg/?url=http%3A%2F%2Fnpr-brightspot.s3.amazonaws.com%2Ff4%2F99%2F7f300fdd4a068052973c3476c06c%2Ffcbcf713-d95e-4f4d-9f27-8bf889ae5ae0.jpg',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $id5 = Str::uuid()->toString();
        $playerIds[] = $id5;
        $users[] = [
            'id' => $id5,
            'first_name' => 'Raph',
            'last_name' => 'GrosPD',
            'email' => 'raph@mybad.test',
            'password' => Hash::make('password'),
            'profile_picture' => 'https://randomuser.me/api/portraits/men/22.jpg',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $id6 = Str::uuid()->toString();
        $playerIds[] = $id6;
        $users[] = [
            'id' => $id6,
            'first_name' => 'Chloé',
            'last_name' => 'Laurent',
            'email' => 'chloe.laurent@mybad.test',
            'password' => Hash::make('password'),
            'profile_picture' => 'https://randomuser.me/api/portraits/women/31.jpg',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $id7 = Str::uuid()->toString();
        $playerIds[] = $id7;
        $users[] = [
            'id' => $id7,
            'first_name' => 'Nathan',
            'last_name' => 'Leroy',
            'email' => 'nathan.leroy@mybad.test',
            'password' => Hash::make('password'),
            'profile_picture' => 'https://randomuser.me/api/portraits/men/15.jpg',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $id8 = Str::uuid()->toString();
        $playerIds[] = $id8;
        $users[] = [
            'id' => $id8,
            'first_name' => 'Manon',
            'last_name' => 'Roux',
            'email' => 'manon.roux@mybad.test',
            'password' => Hash::make('password'),
            'profile_picture' => 'https://randomuser.me/api/portraits/women/12.jpg',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $id9 = Str::uuid()->toString();
        $playerIds[] = $id9;
        $users[] = [
            'id' => $id9,
            'first_name' => 'Enzo',
            'last_name' => 'Garcia',
            'email' => 'enzo.garcia@mybad.test',
            'password' => Hash::make('password'),
            'profile_picture' => 'https://randomuser.me/api/portraits/men/78.jpg',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $id10 = Str::uuid()->toString();
        $playerIds[] = $id10;
        $users[] = [
            'id' => $id10,
            'first_name' => 'Camille',
            'last_name' => 'David',
            'email' => 'camille.david@mybad.test',
            'password' => Hash::make('password'),
            'profile_picture' => 'https://randomuser.me/api/portraits/women/42.jpg',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('users')->insert($users);

        // 2. Insertion dans admin_users
        $realAdminId = Str::uuid()->toString();
        DB::table('admin_users')->insert([
            'id' => $realAdminId,
            'user_id' => $adminId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Remplacer l'identifiant utilisateur par l'identifiant admin
        // pour que tous les seeders suivants utilisent la clé de AdminUser.
        $adminId = $realAdminId;

        // 3. Insertion dans players
        $players = [];
        $newPlayerIds = [];
        foreach ($playerIds as $index => $userId) {
            $playerId = Str::uuid()->toString();
            $newPlayerIds[] = $playerId;
            $players[] = [
                'id' => $playerId,
                'user_id' => $userId,
                'pin' => Hash::make('1234'),
                'code' => str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('players')->insert($players);

        // Remplacer les identifiants d'utilisateurs par les identifiants de joueurs
        // pour que tous les seeders suivants utilisent la clé de Player et non de User.
        $playerIds = $newPlayerIds;

        // 4. Création des Classes
        $classId = DB::table('school_classes')->insertGetId([
            'school_year' => '2025-2026',
            'name' => 'Lundi Soir - Intermédiaire',
            'address' => 'Gymnase Principal',
            'description' => 'Classe de badminton pour joueurs intermédiaires.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Paramètres d'algorithme ELO
        DB::table('algorithm_parameters')->insert([
            'min_diff' => 10,
            'max_diff' => 200,
            'winner_points' => 25.0,
            'school_class_id' => $classId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. Participants de la classe (tout le monde commence à 100 Elo)
        $elos = [];
        foreach ($playerIds as $pid) {
            $elos[$pid] = 100.0;
        }

        $participants = [];
        foreach ($playerIds as $pid) {
            $participants[] = [
                'participantable_type' => 'App\\Models\\Player',
                'participantable_id' => $pid,
                'elo_rating' => 100.0, // sera mis à jour à la fin
                'school_class_id' => $classId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $participants[] = [
            'participantable_type' => 'App\\Models\\AdminUser',
            'participantable_id' => $adminId,
            'elo_rating' => null,
            'school_class_id' => $classId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('class_participants')->insert($participants);

        // 7. Sessions de classe (8 séances hebdomadaires)
        $sessionIds = [];
        for ($i = 7; $i >= 0; $i--) {
            $sessionIds[] = DB::table('class_sessions')->insertGetId([
                'date' => Carbon::now()->subWeeks($i)->toDateString(),
                'is_active' => $i === 0,
                'school_class_id' => $classId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 8. Matchs réalistes avec Elo cohérent
        // Niveaux de force par joueur (plus c'est haut, plus il gagne souvent)
        $skillLevels = [
            $playerIds[0] => 80,  // Lucas Torres - très fort
            $playerIds[1] => 55,  // Victor Roué - moyen
            $playerIds[2] => 72,  // Antoine Bernard - fort
            $playerIds[3] => 35,  // Serge Lama - faible
            $playerIds[4] => 50,  // Raph - moyen
            $playerIds[5] => 65,  // Chloé Laurent - au-dessus moyenne
            $playerIds[6] => 68,  // Nathan Leroy - au-dessus moyenne
            $playerIds[7] => 40,  // Manon Roux - en-dessous moyenne
            $playerIds[8] => 58,  // Enzo Garcia - moyen+
            $playerIds[9] => 45,  // Camille David - en-dessous moyenne
        ];

        $matchPlayers = [];
        $eloHistories = [];
        $kFactor = 25.0; // K = sensibilité (correspond à winner_points dans algorithm_parameters)

        // Pré-définir des matchups par session pour que chaque joueur joue 2-3 matchs
        $allMatchups = [
            // Session 0
            [[0,3], [1,4], [2,5], [6,7], [8,9]],
            // Session 1
            [[0,1], [2,3], [4,5], [6,9], [7,8]],
            // Session 2
            [[0,6], [1,7], [2,8], [3,9], [4,5]],
            // Session 3
            [[0,2], [1,6], [3,7], [4,8], [5,9]],
            // Session 4
            [[0,5], [1,3], [2,9], [4,6], [7,8]],
            // Session 5
            [[0,4], [1,2], [3,8], [5,7], [6,9]],
            // Session 6
            [[0,7], [1,5], [2,6], [3,4], [8,9]],
            // Session 7
            [[0,1], [2,4], [3,6], [5,8], [7,9]],
        ];

        foreach ($sessionIds as $sIdx => $sessionId) {
            $matchups = $allMatchups[$sIdx];

            foreach ($matchups as $pair) {
                $p1 = $playerIds[$pair[0]];
                $p2 = $playerIds[$pair[1]];

                // Déterminer le vainqueur basé sur les skill levels
                $s1 = $skillLevels[$p1];
                $s2 = $skillLevels[$p2];
                $p1WinChance = $s1 / ($s1 + $s2);
                $roll = mt_rand(1, 100) / 100;
                $p1Wins = $roll < $p1WinChance;

                // Scores réalistes badminton (en 15, gagnant a toujours 15)
                if ($p1Wins) {
                    $score1 = 15;
                    $gap = abs($s1 - $s2);
                    $minLoser = max(3, 13 - intdiv($gap, 5));
                    $score2 = rand(min($minLoser, 13), 13);
                } else {
                    $score2 = 15;
                    $gap = abs($s1 - $s2);
                    $minLoser = max(3, 13 - intdiv($gap, 5));
                    $score1 = rand(min($minLoser, 13), 13);
                }

                $matchId = DB::table('game_matches')->insertGetId([
                    'class_session_id' => $sessionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $matchPlayers[] = [
                    'game_match_id' => $matchId,
                    'player_id' => $p1,
                    'score' => $score1,
                    'validated' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $matchPlayers[] = [
                    'game_match_id' => $matchId,
                    'player_id' => $p2,
                    'score' => $score2,
                    'validated' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Calcul Elo officiel
                // E_A = 1 / (1 + 10^((R_B - R_A) / 400))
                // R_A' = R_A + K × (S_A - E_A)
                $eloBefore1 = $elos[$p1];
                $eloBefore2 = $elos[$p2];

                $expectedA = 1 / (1 + pow(10, ($eloBefore2 - $eloBefore1) / 400));
                $expectedB = 1 - $expectedA;

                $scoreA = $score1 > $score2 ? 1 : 0;
                $scoreB = 1 - $scoreA;

                $elos[$p1] = round($eloBefore1 + $kFactor * ($scoreA - $expectedA), 2);
                $elos[$p2] = round($eloBefore2 + $kFactor * ($scoreB - $expectedB), 2);

                $eloHistories[] = [
                    'elo_before' => $eloBefore1,
                    'elo_after' => $elos[$p1],
                    'player_id' => $p1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $eloHistories[] = [
                    'elo_before' => $eloBefore2,
                    'elo_after' => $elos[$p2],
                    'player_id' => $p2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('match_player')->insert($matchPlayers);
        DB::table('elo_histories')->insert($eloHistories);

        // Mettre à jour les elo_rating finaux des participants
        foreach ($elos as $pid => $elo) {
            DB::table('class_participants')
                ->where('participantable_type', 'App\\Models\\Player')
                ->where('participantable_id', $pid)
                ->update(['elo_rating' => $elo]);
        }
    }
}