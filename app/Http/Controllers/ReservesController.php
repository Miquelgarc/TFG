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
            'property_id' => 'required|exists:rental_properties,id',
            'check_in_date' => 'required|date|after:yesterday',
            'check_out_date' => 'required|date|after:start_date',
        ]);

        $house = Property::findOrFail($request->property_id);

        $nits = \Carbon\Carbon::parse($request->check_in_date)->diffInDays($request->check_out_date);
        $total = $nits * $house->price_per_night;

        if ($request->affiliate_link_id) {
            $link = Link::where('generated_url', $request->url)->firstOrFail();
            $comision = Comisions::where('affiliate_id', $link->affiliate_id)->first();
            if ($comision) {
                $total += $comision->amount;
            }
        }

        $reservation = Reservation::create([
            'user_id' => Auth::id(),
            'property_id' => $house->id,
            'affiliate_link_id' => $link->id ?? null,
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'total_price' => $total,
            'status' => 'pending',
        ]);

        return redirect()->route('reservations.index', [
            'message' => 'Reservation created successfully',
            'reservation' => $reservation,
        ]);
    }
    public function Reserva()
    {
        $houses = Reservation::with(['house', 'user'])->get();
        return Inertia::render('Reserva', [
            'houses' => $houses
        ]);
    }
    public function index()
    {
        $user = Auth::user();

        if ($user->role_name === 'afiliat') {
            $reservations = Reservation::where('user_id', $user->id)->with(['property'])->get();
        } elseif ($user->role_name === 'admin') {
            $reservations = Reservation::with(['property'])->get();
        } else {
            return redirect()->route('home');
        }
        // Filtros de búsqueda
        if ($search = request('search')) {
            $reservations->whereHas('property', function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%");
            });
        }
        if ($date = request('date')) {
            $reservations->whereDate('created_at', $date);
        }
        if ($status = request('status')) {
            $reservations->where('status', $status);
        }

        return Inertia::render('Reserva', [
            'reservations' => $reservations,
        ]);
    }

    public function indexProperties()
    {
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
