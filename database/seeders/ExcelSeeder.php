<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
                'code'       => str_pad($index + 1, 6, '0', STR_PAD_LEFT),
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

        // --- Classe ---
        $classId = DB::table('school_classes')->insertGetId([
            'school_year' => '2025-2026',
            'name'        => 'Cours du Mardi Soir',
            'address'     => 'Gymnase Municipal',
            'description' => 'Cours de badminton du mardi soir.',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // --- Paramètres algorithme ELO ---
        $algoParams = [
            ['min_diff' => -20, 'max_diff' => -12, 'winner_points' => -0.7],
            ['min_diff' => -11, 'max_diff' => -7,  'winner_points' => -0.2],
            ['min_diff' => -6,  'max_diff' => -1,  'winner_points' => 0.0],
            ['min_diff' => 0,   'max_diff' => 6,   'winner_points' => 0.5],
            ['min_diff' => 7,   'max_diff' => 11,  'winner_points' => 1.0],
            ['min_diff' => 12,  'max_diff' => 20,  'winner_points' => 1.6],
        ];
        DB::table('algorithm_parameters')->insert(
            array_map(fn($p) => [...$p, 'school_class_id' => $classId, 'created_at' => now(), 'updated_at' => now()], $algoParams)
        );

        // --- Participants (joueurs + admin) ---
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

        // --- Séance ---
        DB::table('class_sessions')->insert([
            'date'            => now()->toDateString(),
            'is_active'       => true,
            'school_class_id' => $classId,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }
}