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
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Reservation::query()->with(['property', 'user']);

        // Filtro por tipo de usuario
        if ($user->role_name === 'afiliat') {
            $query->where('user_id', $user->id);
        } elseif ($user->role_name !== 'admin') {
            return redirect()->route('home');
        }

        // Filtros dinámicos
        if ($search = $request->input('search')) {
            $query->whereHas('property', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($from = $request->input('date_from')) {
            $query->whereDate('check_in_date', '>=', $from);
        }

        if ($to = $request->input('date_to')) {
            $query->whereDate('check_out_date', '<=', $to);
        }

        // Paginar resultados
        $reservations = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->all());

        return Inertia::render('Reservas', [
            'reservas' => $reservations,
            'filtersReseras' => $request->only(['search', 'status', 'date_from', 'date_to', 'page']),
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
