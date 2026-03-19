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

        // 6. Participants de la classe
        $participants = [];
        foreach ($playerIds as $pid) {
            $participants[] = [
                'participantable_type' => 'App\\Models\\Player',
                'participantable_id' => $pid,
                'elo_rating' => rand(90, 120),
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

        // 7. Sessions de classe
        $sessionIds = [];
        for ($i = 0; $i < 5; $i++) {
            $sessionIds[] = DB::table('class_sessions')->insertGetId([
                'date' => Carbon::now()->subDays(7 * $i)->toDateString(),
                'is_active' => $i === 0, // La plus récente est active
                'school_class_id' => $classId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 8. Matchs, Joueurs de match & Historique ELO
        $matchPlayers = [];
        $eloHistories = [];

        foreach ($sessionIds as $sessionId) {
            // 3 matchs par session
            for ($m = 0; $m < 3; $m++) {
                $matchId = DB::table('game_matches')->insertGetId([
                    'class_session_id' => $sessionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Choix de 2 joueurs aléatoirement
                $p1 = $playerIds[array_rand($playerIds)];
                $p2 = $playerIds[array_rand($playerIds)];
                while ($p1 === $p2) {
                    $p2 = $playerIds[array_rand($playerIds)];
                }

                $score1 = rand(7, 15);
                $score2 = $score1 === 15 ? rand(5, 13) : 15;

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

                // Faux historique ELO
                $eloP1Base = rand(90, 110);
                $eloP2Base = rand(90, 110);

                $eloHistories[] = [
                    'elo_before' => $eloP1Base,
                    'elo_after' => $score1 > $score2 ? $eloP1Base + 2.5 : $eloP1Base - 2.5,
                    'player_id' => $p1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $eloHistories[] = [
                    'elo_before' => $eloP2Base,
                    'elo_after' => $score2 > $score1 ? $eloP2Base + 2.5 : $eloP2Base - 2.5,
                    'player_id' => $p2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('match_player')->insert($matchPlayers);
        DB::table('elo_histories')->insert($eloHistories);
    }
}
