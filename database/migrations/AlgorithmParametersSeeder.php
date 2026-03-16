<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlgorithmParametersSeeder extends Seeder
{
    /**
     * Lors de la création d'une nouvelle "classe", on crée automatiquement
     * ces 6 lignes dans algorithm_parameters (comme indiqué dans le diagramme).
     * 
     * À appeler depuis un Observer sur le modèle Class, ou via ce seeder de référence.
     */
    public function run(): void
    {
        $defaultParams = [
            ['min_diff' => -20, 'max_diff' => -12, 'winner_points' => -0.7],
            ['min_diff' => -11, 'max_diff' => -7,  'winner_points' => -0.2],
            ['min_diff' => -6,  'max_diff' => -1,  'winner_points' =>  0.0],
            ['min_diff' =>  0,  'max_diff' =>  6,  'winner_points' =>  0.5],
            ['min_diff' =>  7,  'max_diff' => 11,  'winner_points' =>  1.0],
            ['min_diff' => 12,  'max_diff' => 20,  'winner_points' =>  1.6],
        ];

        // Exemple d'utilisation dans un Observer (ClassObserver.php) :
        // public function created(ClassModel $class): void {
        //     foreach ($this->defaultParams as $params) {
        //         $class->algorithmParameters()->create($params);
        //     }
        // }

        // Ou ici pour une classe donnée :
        // DB::table('algorithm_parameters')->insert(
        //     array_map(fn($p) => [...$p, 'class_id' => $classId], $defaultParams)
        // );
    }
}
