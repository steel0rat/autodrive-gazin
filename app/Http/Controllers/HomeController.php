<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\CarMark;

class HomeController extends Controller
{
    public function index()
    {
        dd(CarMark::all()->first()->car);
        dd(Car::all()->first()->mark);
    }
}
