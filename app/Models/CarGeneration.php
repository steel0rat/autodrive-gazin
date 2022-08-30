<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property int    $mark_id
 * @property int    $model_id
 * @property string $caption
 * @property string $code
 */
class CarGeneration extends Model
{
    protected $table = 'car_generation';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'mark_id', 'model_id', 'caption', 'code'
    ];

    protected $hidden = [

    ];

    protected $casts = [
        'id' => 'int', 'mark_id' => 'int', 'model_id' => 'int', 'caption' => 'string', 'code' => 'string'
    ];

    protected $dates = [

    ];

    public $timestamps = true;

    public function car()
    {
        return $this->hasMany(Car::class, 'model_generation_id', 'id');
    }

    public function mark()
    {
        return $this->hasOne(CarMark::class, 'id', 'mark_id');
    }

    public function model()
    {
        return $this->hasOne(CarModel::class, 'id', 'model_id');
    }
}
