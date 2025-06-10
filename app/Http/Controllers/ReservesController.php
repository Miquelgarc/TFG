<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Commission;
use App\Models\Link;

class ReservesController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:rental_properties,id',
            'check_in_date' => 'required|date|after:yesterday',
            'check_out_date' => 'required|date|after:check_in_date',
        ], [
            'property_id.required' => 'La propietat és obligatòria.',
            'property_id.exists' => 'La propietat seleccionada no existeix.',
            'check_in_date.required' => 'La data d\'entrada és obligatòria.',
            'check_in_date.date' => 'La data d\'entrada ha de ser una data vàlida.',
            'check_in_date.after' => 'La data d\'entrada ha de ser posterior a avui.',
            'check_out_date.required' => 'La data de sortida és obligatòria.',
            'check_out_date.date' => 'La data de sortida ha de ser una data vàlida.',
            'check_out_date.after' => 'La data de sortida ha de ser posterior a la data d\'entrada.',
        ]);
        if (Auth::id() === null) {
            $userID = 12; // ID de l'usuari per defecte
        } else {
            $userID = Auth::id();
        }

        $house = Property::findOrFail($request->property_id);

        $nits = \Carbon\Carbon::parse($request->check_in_date)->diffInDays($request->check_out_date);
        $total = $nits * $house->price_per_night;
        $affiliateLink = null;
        $commissionAmount = 0;

        if ($request->filled('affiliate_link_id')) {
            $affiliateLink = Link::with('affiliate.currentAffiliateContract.level')
                ->find($request->affiliate_link_id);

            if ($affiliateLink && $affiliateLink->affiliate && $affiliateLink->affiliate->currentAffiliateContract) {
                $percentage = $affiliateLink->affiliate->currentAffiliateContract->level->commission_percentage ?? 0;
                $commissionAmount = round(($total * $percentage) / 100, 2);
                $affiliateLink->increment('conversions');
            }
        }

        $reservation = Reservation::create([
            'user_id' => $userID,
            'property_id' => $house->id,
            'affiliate_link_id' => $affiliateLink->id ?? null,
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'total_price' => $total,
            'status' => 'pending',
        ]);

        if ($affiliateLink && $commissionAmount > 0) {
            Commission::create([
                'affiliate_id' => $affiliateLink->affiliate_id,
                'reservation_id' => $reservation->id,
                'amount' => $commissionAmount,
                'description' => 'Comisió per reserva al ' . $house->title,
                'generated_at' => now(),
                'status' => 'pending',
                'is_paid' => false,
            ]);
        }
        return redirect()->back()->with('success', 'Reserva creada correctamente.');
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

        $query = Reservation::query()->with([
            'property',
            'user',
            'affiliateLink.affiliate' // el afiliado
        ]);

        // FILTRO POR TIPO DE USUARIO
        if ($user->role_name === 'affiliate') {
            // Solo ver reservas relacionadas a los links del afiliado
            $query->whereHas('affiliateLink', function ($q) use ($user) {
                $q->where('affiliate_id', $user->id);
            });
        }

        // FILTROS DINÁMICOS
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search, $user) {
                // Buscar por título de propiedad
                $q->whereHas('property', function ($q2) use ($search) {
                    $q2->where('title', 'like', "%{$search}%");
                });

                // Si es admin, buscar también por nombre del afiliado
                if ($user->role_name === 'admin') {
                    $q->orWhereHas('affiliateLink.user', function ($q3) use ($search) {
                        $q3->where('name', 'like', "%{$search}%");
                    });
                }
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

        // PAGINAR
        $reservations = $query->orderByDesc('created_at')->paginate(10)->appends($request->all());

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
