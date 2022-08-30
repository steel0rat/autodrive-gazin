<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $caption
 * @property string $code
 */
class CarMark extends Model
{
    protected $table = 'car_mark';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'caption', 'code'
    ];

    protected $hidden = [

    ];

    protected $casts = [
        'id' => 'int', 'caption' => 'string', 'code' => 'string'
    ];

    protected $dates = [

    ];

    public $timestamps = true;

    public function car()
    {
        return $this->hasMany(Car::class, 'mark_id', 'id');
    }

    public function model()
    {
        return $this->hasMany(CarModel::class, 'mark_id', 'id');
    }

    public function generation()
    {
        return $this->hasMany(CarGeneration::class, 'mark_id', 'id');
    }
}
