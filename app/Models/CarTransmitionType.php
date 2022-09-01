<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $caption
 * @property string $code
 */
class CarTransmitionType extends Model
{
    protected $table = 'car_transmition_type';

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

    public $timestamps = false;

    public function car()
    {
        return $this->hasMany(Car::class, 'transmition_type_id', 'id');
    }
}
