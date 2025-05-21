<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HouseController extends Controller
{
    public function indexProperties() {
    $properties = Property::all();
    return Inertia::render('Reserva', [
        'houses' => $properties,
    ]);
}
}
