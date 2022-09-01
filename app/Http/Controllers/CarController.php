<?php

namespace App\Http\Controllers;

use App\Http\Controllers\EntityUpdater\EntityUpdater;
use App\Models\Car;

class CarController extends Controller
{
    const PRIMATY_KEYS = [
        'car_id',
        'generation_id'
    ];

    const INDEX_KEYS = [
        'year',
        'run',
        'mark_id',
        'model_id',
        'model_generation_id',
        'color_id',
        'engine_type_id',
        'transmition_type_id',
        'gear_type_id',
        'body_type_id'
    ];


    public function updateCarTable($newModelsArr):array
    {
        $entityUpdater = new EntityUpdater(Car::class,self::PRIMATY_KEYS,self::INDEX_KEYS);
        return $entityUpdater->updateEntities($newModelsArr);
    }
}

