<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Comisions;
use App\Models\Link;

class ReservesController extends Controller
{
     public function store(Request $request)
    {
        $request->validate([
            'house_id' => 'required|exists:houses,id',
            'start_date' => 'required|date|after:yesterday',
            'end_date' => 'required|date|after:start_date',
        ]);

        $house = Property::findOrFail($request->house_id);

        $nits = now()->parse($request->start_date)->diffInDays($request->end_date);
        $total = $nits * $house->price_per_night;

        $reservation = Reservation::create([
            'user_id' => Auth::id(),
            'house_id' => $house->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_price' => $total,
        ]);

        return response()->json($reservation);
    }
    public function Reserva()
    {
        $houses = Reservation::with(['house', 'user'])->get();
        return Inertia::render('Reserva', [
            'houses' => $houses
        ]);
    }

    public function indexProperties() {
        $properties = Property::all();

        return Inertia::render('Reserva', [
            'houses' => $properties,
        ]);
    }
    /* public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price_per_night' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
        ])
    } */
}
