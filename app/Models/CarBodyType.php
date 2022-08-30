<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $caption
 * @property string $code
 */
class CarBodyType extends Model
{
    protected $table = 'car_body_type';

    protected $primaryKey = '';

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
        return $this->hasMany(Car::class, 'body_type_id', 'id');
    }
}
