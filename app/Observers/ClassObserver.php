<?php

namespace App\Observers;

use App\Models\AlgorithmParameter;
use App\Models\ClassSession;
use App\Models\SchoolClass;

class ClassObserver
{
    /**
     * Handle the Class "created" event.
     */
    public function created(SchoolClass $class): void
    {
        $defaultParams = [
            ['min_diff' => -20, 'max_diff' => -12, 'winner_points' => -7],
            ['min_diff' => -11, 'max_diff' => -7, 'winner_points' => -2],
            ['min_diff' => -6, 'max_diff' => -1, 'winner_points' => 0],
            ['min_diff' => 0, 'max_diff' => 6, 'winner_points' => 5],
            ['min_diff' => 7, 'max_diff' => 11, 'winner_points' => 10],
            ['min_diff' => 12, 'max_diff' => 20, 'winner_points' => 16],
        ];

        foreach ($defaultParams as $param) {
            AlgorithmParameter::create([
                'school_class_id' => $class->id,
                'min_diff' => $param['min_diff'],
                'max_diff' => $param['max_diff'],
                'winner_points' => $param['winner_points'],
            ]);
        }
    }

    /**
     * Handle the Class "updated" event.
     */
    public function updated(SchoolClass $class): void
    {
        //
    }

    /**
     * Handle the Class "deleted" event.
     */
    public function deleted(SchoolClass $class): void
    {
        //
    }

    /**
     * Handle the Class "restored" event.
     */
    public function restored(SchoolClass $class): void
    {
        //
    }

    /**
     * Handle the Class "force deleted" event.
     */
    public function forceDeleted(SchoolClass $class): void
    {
        //
    }
}
