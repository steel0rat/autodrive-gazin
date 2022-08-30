<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property int    $mark_id
 * @property string $caption
 * @property string $code
 */
class CarModel extends Model
{
    protected $table = 'car_model';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'mark_id', 'caption', 'code'
    ];

    protected $hidden = [

    ];

    protected $casts = [
        'id' => 'int', 'mark_id' => 'int', 'caption' => 'string', 'code' => 'string'
    ];

    protected $dates = [

    ];

    public $timestamps = true;

    public function car()
    {
        return $this->hasMany(Car::class, 'model_id', 'id');
    }

    public function mark()
    {
        return $this->hasOne(CarMark::class, 'id', 'mark_id');
    }

    public function generation()
    {
        return $this->hasMany(CarGeneration::class, 'mark_id', 'id');
    }
}
