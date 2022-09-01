<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $table = 'car';

    protected $primaryKey = 'id';

    protected $fillable = [
        'car_id', 'generation_id', 'mark_id', 'model_id', 'model_generation_id', 'year', 'run', 'color_id', 'engine_type_id', 'transmition_type_id', 'gear_type_id', 'body_type_id'
    ];

    protected $hidden = [

    ];

    protected $casts = [
        'mark_id' => 'int', 'model_id' => 'int', 'model_generation_id' => 'int', 'year' => 'int', 'run' => 'int', 'color_id' => 'int', 'engine_type_id' => 'int', 'transmition_type_id' => 'int', 'gear_type_id' => 'int', 'body_type_id' => 'int'
    ];

    protected $dates = [

    ];

    public $timestamps = false;

    public function mark()
    {
        return $this->belongsTo(CarMark::class);
    }

    public function model()
    {
        return $this->belongsTo(CarModel::class);
    }

    public function model_generation()
    {
        return $this->belongsTo(CarGeneration::class);
    }

    public function color()
    {
        return $this->belongsTo(CarColor::class);
    }

    public function bodyType()
    {
        return $this->belongsTo(CarBodyType::class);
    }
    public function engineType()
    {
        return $this->belongsTo(CarEngineType::class);
    }
    public function transmitionType()
    {
        return $this->belongsTo(CarTransmitionType::class);
    }
    public function gearType()
    {
        return $this->belongsTo(CarGearType::class);
    }
}
