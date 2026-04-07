<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ExcelSeeder extends Seeder
{
    public function run(): void
    {
        // Données des joueurs : prénom (tel que dans l'excel), nom inventé, elo
        $playersData = [
            ['Sean',             'Lefèvre',    109.0],
            ['Maxime V',         'Verdier',    108.7],
            ['Alexis',           'Morin',      106.7],
            ['Alexandre Le Boss','Dupont',     106.4],
            ['Guénolé',          'Kervadec',   106.4],
            ['Marceau',          'Blanchard',  105.6],
            ['Antoine',          'Girard',     105.5],
            ['Dimitri',          'Pavlov',     105.4],
            ['Stevan',           'Renaud',     105.0],
            ['Emma',             'Leclerc',    104.6],
            ['Sara',             'Benmoussa',  104.6],
            ['Arthur Le + Boss', 'Martin',     104.2],
            ['Timéo',            'Gauthier',   104.1],
            ['Ethan',            'Collet',     103.7],
            ['Elisa',            'Fournier',   103.5],
            ['Tanguy T',         'Tanguy',     103.5],
            ['Luc',              'Hervet',     102.9],
            ['Tom',              'Prigent',    102.7],
            ['Célian',           'Rousseau',   102.6],
            ['Théo',             'Lemaire',    102.3],
            ['Adrian',           'Costa',      102.0],
            ['Camille',          'Perrin',     101.4],
            ['Jane',             'Morvan',     101.1],
            ['Yaël',             'Abgrall',    101.1],
            ['Malo',             'Le Gall',    100.9],
            ['Pierre',           'Jacq',       100.8],
            ['Vianney',          'Cariou',     100.5],
            ['Enora',            'Le Bihan',   100.4],
            ['Corentin',         'Maze',       100.3],
            ['Lisa',             'Dubois',      99.8],
            ['Robin',            'Carpentier',  99.8],
            ['Romain W',         'Wagner',      99.7],
            ['Léo',              'Bertrand',    99.6],
            ['Mathieu',          'Cozic',       99.2],
            ['Hugo',             'Petit',       99.1],
            ['Ioane',            'Tehei',       98.7],
            ['Maximilien',       'Faure',       98.3],
            ['Lohan',            'Joly',        98.2],
            ['Alexie',           'Riou',        98.1],
            ['Tanguy C',         'Conan',       97.5],
            ['Ewenn',            'Guéguen',     97.1],
            ['Alexandre T',      'Trolez',      97.0],
            ['Clem',             'Nédélec',     96.9],
        ];

        // --- Admin ---
        $adminUserId = Str::uuid()->toString();
        DB::table('users')->insert([
            'id'              => $adminUserId,
            'first_name'      => 'Admin',
            'last_name'       => 'MyBAD',
            'email'           => 'admin@mybad.test',
            'password'        => Hash::make('password'),
            'profile_picture' => null,
            'is_active'       => true,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        $adminId = Str::uuid()->toString();
        DB::table('admin_users')->insert([
            'id'         => $adminId,
            'user_id'    => $adminUserId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // --- Joueurs ---
        $userRows = [];
        $playerRows = [];
        $participantRows = [];
        $playerIds = [];

        foreach ($playersData as $index => [$firstName, $lastName, $elo]) {
            $userId = Str::uuid()->toString();
            $playerId = Str::uuid()->toString();
            $playerIds[] = $playerId;

            // Email basé sur le prénom nettoyé : "Maxime V" -> "maximev", "Alexandre Le Boss" -> "alexandreleboss"
            $emailSlug = strtolower(preg_replace('/[^a-zA-Z]/', '', $firstName));

            $userRows[] = [
                'id'              => $userId,
                'first_name'      => $firstName,
                'last_name'       => $lastName,
                'email'           => $emailSlug . '@mybad.test',
                'password'        => Hash::make('password'),
                'profile_picture' => null,
                'is_active'       => true,
                'created_at'      => now(),
                'updated_at'      => now(),
            ];

            $playerRows[] = [
                'id'         => $playerId,
                'user_id'    => $userId,
                'pin'        => Hash::make('1234'),
                'code'       => str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $participantRows[] = [
                'participantable_type' => 'App\\Models\\Player',
                'participantable_id'   => $playerId,
                'elo_rating'           => $elo,
                'school_class_id'      => null, // set after class creation
                'created_at'           => now(),
                'updated_at'           => now(),
            ];
        }

        DB::table('users')->insert($userRows);
        DB::table('players')->insert($playerRows);

        // --- Classes ---
        $classId = DB::table('school_classes')->insertGetId([
            'school_year' => '2025-2026',
            'name'        => 'Cours du Mardi Soir',
            'address'     => 'Gymnase Municipal',
            'description' => 'Cours de badminton du mardi soir.',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $classId2 = DB::table('school_classes')->insertGetId([
            'school_year' => '2025-2026',
            'name'        => 'Cours du Jeudi Soir',
            'address'     => 'Gymnase des Sports',
            'description' => 'Cours de badminton du jeudi soir.',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $classId3 = DB::table('school_classes')->insertGetId([
            'school_year' => '2025-2026',
            'name'        => 'Cours du Samedi Matin',
            'address'     => 'Complexe Sportif',
            'description' => 'Cours de badminton du samedi matin.',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // --- Paramètres algorithme ELO (pour les 3 classes) ---
        $algoParams = [
            ['min_diff' => -20, 'max_diff' => -12, 'winner_points' => -0.7],
            ['min_diff' => -11, 'max_diff' => -7,  'winner_points' => -0.2],
            ['min_diff' => -6,  'max_diff' => -1,  'winner_points' => 0.0],
            ['min_diff' => 0,   'max_diff' => 6,   'winner_points' => 0.5],
            ['min_diff' => 7,   'max_diff' => 11,  'winner_points' => 1.0],
            ['min_diff' => 12,  'max_diff' => 20,  'winner_points' => 1.6],
        ];
        foreach ([$classId, $classId2, $classId3] as $cid) {
            DB::table('algorithm_parameters')->insert(
                array_map(fn($p) => [...$p, 'school_class_id' => $cid, 'created_at' => now(), 'updated_at' => now()], $algoParams)
            );
        }

        // 6. Règles et défis par défaut
        $ruleId = DB::table('rules')->insertGetId([
            'name' => 'Défis',
            'enable_ranking_groups' => false,
            'enable_elo_handicap' => false,
            'group_size' => null,
            'school_class_id' => $classId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $defaultHandicaps = [
            ['min_gap' => 1,  'max_gap' => 3,  'handicap' => 0],
            ['min_gap' => 3,  'max_gap' => 6,  'handicap' => -3],
            ['min_gap' => 6,  'max_gap' => 9,  'handicap' => -5],
            ['min_gap' => 10, 'max_gap' => 0, 'handicap' => -7]
        ];
        DB::table('handicap_parameters')->insert(
            array_map(fn($h) => [...$h, 'rule_id' => $ruleId, 'created_at' => now(), 'updated_at' => now()], $defaultHandicaps)
        );

        // --- Répartition des joueurs ---
        // Tous dans le cours 1, première moitié dans le cours 2, seconde moitié dans le cours 3
        $half = intdiv(count($playerIds), 2);
        $class2PlayerIds = array_slice($playerIds, 0, $half);
        $class3PlayerIds = array_slice($playerIds, $half);

        // --- Participants classe 1 (tous les joueurs + admin) ---
        foreach ($participantRows as &$row) {
            $row['school_class_id'] = $classId;
        }
        unset($row);

        $participantRows[] = [
            'participantable_type' => 'App\\Models\\AdminUser',
            'participantable_id'   => $adminId,
            'elo_rating'           => null,
            'school_class_id'      => $classId,
            'created_at'           => now(),
            'updated_at'           => now(),
        ];

        DB::table('class_participants')->insert($participantRows);

        // --- Participants classe 2 (première moitié + admin) ---
        $class2Participants = [];
        foreach ($class2PlayerIds as $pid) {
            $class2Participants[] = [
                'participantable_type' => 'App\\Models\\Player',
                'participantable_id'   => $pid,
                'elo_rating'           => 100.0,
                'school_class_id'      => $classId2,
                'created_at'           => now(),
                'updated_at'           => now(),
            ];
        }
        $class2Participants[] = [
            'participantable_type' => 'App\\Models\\AdminUser',
            'participantable_id'   => $adminId,
            'elo_rating'           => null,
            'school_class_id'      => $classId2,
            'created_at'           => now(),
            'updated_at'           => now(),
        ];
        DB::table('class_participants')->insert($class2Participants);

        // --- Participants classe 3 (seconde moitié + admin) ---
        $class3Participants = [];
        foreach ($class3PlayerIds as $pid) {
            $class3Participants[] = [
                'participantable_type' => 'App\\Models\\Player',
                'participantable_id'   => $pid,
                'elo_rating'           => 100.0,
                'school_class_id'      => $classId3,
                'created_at'           => now(),
                'updated_at'           => now(),
            ];
        }
        $class3Participants[] = [
            'participantable_type' => 'App\\Models\\AdminUser',
            'participantable_id'   => $adminId,
            'elo_rating'           => null,
            'school_class_id'      => $classId3,
            'created_at'           => now(),
            'updated_at'           => now(),
        ];
        DB::table('class_participants')->insert($class3Participants);

        // --- Barème elo (même que algorithm_parameters) ---
        $bareme = [
            ['min' => -20, 'max' => -12, 'base' => -0.7],
            ['min' => -11, 'max' => -7,  'base' => -0.2],
            ['min' => -6,  'max' => -1,  'base' => 0.0],
            ['min' => 0,   'max' => 6,   'base' => 0.5],
            ['min' => 7,   'max' => 11,  'base' => 1.0],
            ['min' => 12,  'max' => 20,  'base' => 1.6],
        ];

        $vlookup = function (int $ecart) use ($bareme): float {
            foreach ($bareme as $row) {
                if ($ecart >= $row['min'] && $ecart <= $row['max']) {
                    return $row['base'];
                }
            }
            return 0.0;
        };

        // Skill levels pour déterminer les vainqueurs
        $skillLevels = [];
        foreach ($playersData as $index => [$fn, $ln, $elo]) {
            $skillLevels[$playerIds[$index]] = $elo;
        }

        // --- Génération séances + matchs pour chaque cours ---
        $classConfigs = [
            ['classId' => $classId,  'classPlayerIds' => $playerIds,       'startElos' => array_combine($playerIds, array_column($playersData, 2))],
            ['classId' => $classId2, 'classPlayerIds' => $class2PlayerIds, 'startElos' => array_fill_keys($class2PlayerIds, 100.0)],
            ['classId' => $classId3, 'classPlayerIds' => $class3PlayerIds, 'startElos' => array_fill_keys($class3PlayerIds, 100.0)],
        ];

        foreach ($classConfigs as $config) {
            $cid = $config['classId'];
            $cPlayerIds = $config['classPlayerIds'];
            $elos = $config['startElos'];
            $cPlayerCount = count($cPlayerIds);

            // Map player_id → participant_id pour cette classe
            $participantIdByPlayer = DB::table('class_participants')
                ->where('school_class_id', $cid)
                ->where('participantable_type', 'App\\Models\\Player')
                ->pluck('id', 'participantable_id');

            // Séances (8 semaines)
            $sessionIds = [];
            for ($i = 7; $i >= 0; $i--) {
                $sessionIds[] = DB::table('class_sessions')->insertGetId([
                    'date'            => Carbon::now()->subWeeks($i)->toDateString(),
                    'is_active'       => $i === 0,
                    'school_class_id' => $cid,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }

            $matchPlayers = [];
            $eloHistories = [];

            foreach ($sessionIds as $sessionId) {
                $indices = range(0, $cPlayerCount - 1);
                shuffle($indices);

                $matchups = [];
                for ($i = 0; $i + 1 < $cPlayerCount; $i += 2) {
                    $matchups[] = [$indices[$i], $indices[$i + 1]];
                }

                foreach ($matchups as $pair) {
                    $p1 = $cPlayerIds[$pair[0]];
                    $p2 = $cPlayerIds[$pair[1]];

                    $s1 = $skillLevels[$p1];
                    $s2 = $skillLevels[$p2];
                    $p1WinChance = $s1 / ($s1 + $s2);
                    $roll = mt_rand(1, 100) / 100;
                    $p1Wins = $roll < $p1WinChance;

                    if ($p1Wins) {
                        $score1 = 15;
                        $gap = abs($s1 - $s2);
                        $minLoser = max(3, 13 - intdiv((int) $gap, 2));
                        $score2 = rand(min($minLoser, 13), 13);
                    } else {
                        $score2 = 15;
                        $gap = abs($s1 - $s2);
                        $minLoser = max(3, 13 - intdiv((int) $gap, 2));
                        $score1 = rand(min($minLoser, 13), 13);
                    }

                    $matchId = DB::table('game_matches')->insertGetId([
                        'class_session_id' => $sessionId,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);

                    $matchPlayers[] = [
                        'game_match_id' => $matchId,
                        'player_id'     => $p1,
                        'score'         => $score1,
                        'validated'     => true,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];
                    $matchPlayers[] = [
                        'game_match_id' => $matchId,
                        'player_id'     => $p2,
                        'score'         => $score2,
                        'validated'     => true,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];

                    $eloBefore1 = $elos[$p1];
                    $eloBefore2 = $elos[$p2];
                    $ecart = abs($score1 - $score2);

                    if ($score1 > $score2) {
                        $bonusP1 = $vlookup($ecart) + $ecart / 10;
                        $malusP2 = $vlookup(-$ecart) - $ecart / 10;
                        $elos[$p1] = round($eloBefore1 + $bonusP1, 2);
                        $elos[$p2] = round($eloBefore2 + $malusP2, 2);
                    } else {
                        $bonusP2 = $vlookup($ecart) + $ecart / 10;
                        $malusP1 = $vlookup(-$ecart) - $ecart / 10;
                        $elos[$p1] = round($eloBefore1 + $malusP1, 2);
                        $elos[$p2] = round($eloBefore2 + $bonusP2, 2);
                    }

                    $eloHistories[] = [
                        'participant_id' => $participantIdByPlayer[$p1],
                        'game_match_id'  => $matchId,
                        'elo_before'     => $eloBefore1,
                        'elo_after'      => $elos[$p1],
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];
                    $eloHistories[] = [
                        'participant_id' => $participantIdByPlayer[$p2],
                        'game_match_id'  => $matchId,
                        'elo_before'     => $eloBefore2,
                        'elo_after'      => $elos[$p2],
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];
                }
            }

            DB::table('match_player')->insert($matchPlayers);
            DB::table('elo_histories')->insert($eloHistories);

            foreach ($elos as $pid => $elo) {
                DB::table('class_participants')
                    ->where('participantable_type', 'App\\Models\\Player')
                    ->where('participantable_id', $pid)
                    ->where('school_class_id', $cid)
                    ->update(['elo_rating' => $elo]);
            }
        }
    }
}
