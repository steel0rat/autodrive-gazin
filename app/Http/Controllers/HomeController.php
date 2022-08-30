<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\CarMark;
use App\Models\CarModel;
use App\Models\CarGeneration;
use App\Models\CarColor;
use App\Models\CarBodyType;
use App\Models\CarEngineType;
use App\Models\CarTransmitionType;
use App\Models\CarGearType;

class HomeController extends Controller
{
    public function index()
    {
        dd(Car::all()->first());
    }
}
