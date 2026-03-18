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
            'profile_picture' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // --- Joueurs ---
        $playerIds = [];
        $firstNames = ['Lucas', 'Victor', 'Antoine', 'Serge', 'Raph', 'Chloé', 'Nathan', 'Manon', 'Enzo', 'Camille'];
        $lastNames = ['Torres', 'Roué', 'Bernard', 'Lama', 'GrosPD', 'Laurent', 'Leroy', 'Roux', 'Garcia', 'David'];

        for ($i = 0; $i < 10; $i++) {
            $id = Str::uuid()->toString();
            $playerIds[] = $id;
            $users[] = [
                'id' => $id,
                'first_name' => $firstNames[$i],
                'last_name' => $lastNames[$i],
                'email' => strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $firstNames[$i])) . '@mybad.test',
                'password' => Hash::make('password'),
                'profile_picture' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('users')->insert($users);

        // 2. Insertion dans admin_users
        DB::table('admin_users')->insert([
            'user_id' => $adminId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Insertion dans players
        $players = [];
        foreach ($playerIds as $index => $pid) {
            $players[] = [
                'id' => $pid,
                'pin' => Hash::make('1234'),
                'code' => 'P' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('players')->insert($players);

        // 4. Création des Classes
        $classId = DB::table('classes')->insertGetId([
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
            'class_id' => $classId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. Participants de la classe
        $participants = [];
        foreach ($playerIds as $pid) {
            $participants[] = [
                'participantable_type' => 'App\\Models\\Player',
                'participantable_id' => $pid,
                'elo_rating' => rand(800, 1500),
                'class_id' => $classId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        $participants[] = [
            'participantable_type' => 'App\\Models\\AdminUser',
            'participantable_id' => $adminId,
            'elo_rating' => 1500,
            'class_id' => $classId,
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
                'class_id' => $classId,
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
                $matchId = DB::table('matches')->insertGetId([
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

                $score1 = rand(10, 21);
                $score2 = $score1 === 21 ? rand(5, 19) : 21;

                $matchPlayers[] = [
                    'match_id' => $matchId,
                    'player_id' => $p1,
                    'score' => $score1,
                    'validated' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $matchPlayers[] = [
                    'match_id' => $matchId,
                    'player_id' => $p2,
                    'score' => $score2,
                    'validated' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Faux historique ELO
                $eloP1Base = rand(900, 1100);
                $eloP2Base = rand(900, 1100);
                
                $eloHistories[] = [
                    'elo_before' => $eloP1Base,
                    'elo_after' => $score1 > $score2 ? $eloP1Base + 25 : $eloP1Base - 25,
                    'player_id' => $p1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $eloHistories[] = [
                    'elo_before' => $eloP2Base,
                    'elo_after' => $score2 > $score1 ? $eloP2Base + 25 : $eloP2Base - 25,
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
