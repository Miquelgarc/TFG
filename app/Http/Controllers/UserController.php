<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Commission;
use App\Models\Link;
use App\Models\AffiliateLink;
use App\Models\Property;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Exports\LinksExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Str;


class UserController extends Controller
{
    /**
     * Muestra todos los usuarios con el rol 'afiliat', solo accesible para admins.
     */
    public function indexAfiliats(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role_name !== 'admin') {
            abort(403, 'No tienes permiso para ver esta página.');
        }

        $query = User::with(['currentAffiliateContract.level', 'commissions', 'affiliateLinks.reservations'])
            ->where('role_id', 2);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $status = $request->input('status');
        if (!is_null($status) && $status !== '') {
            $query->where('status', $status);
        }

        $afiliates = $query->paginate(10)->withQueryString();

        // Añadir stats para cada afiliado
        $afiliates->getCollection()->transform(function ($user) {
            $confirmedReservations = $user->affiliateLinks->flatMap->reservations->where('status', 'confirmed');
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'company_name' => $user->company_name,
                'status' => $user->status,
                'level_name' => $user->currentAffiliateContract?->level?->name ?? 'Sin contrato',
                'commission_percentage' => $user->currentAffiliateContract?->level?->commission_percentage ?? 0,
                'total_reservations' => $confirmedReservations->count(),
                'total_earned' => number_format($user->commissions->sum('amount'), 2),
            ];
        });

        return Inertia::render('Afiliates', [
            'afiliates' => $afiliates,
            'filters' => [
                'search' => $request->search,
                'status' => $request->input('status', '')
            ],
        ]);
    }



    public function destroy($id)
    {
        $afiliado = User::findOrFail($id);
        $afiliado->delete();

        return response()->json(['message' => 'Afiliado eliminado']);

    }
    public function changeStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,active,rejected',
        ]);

        $afiliado = User::findOrFail($id);

        $afiliado->status = $request->status;
        $afiliado->save();

        return response()->json(['message' => 'Estado actualizado correctamente']);
    }

    public function Comisions(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'No tienes permiso para ver esta página.');
        }

        $query = Commission::query();

        if ($user->role_name === 'affiliate') {
            $query->leftJoin('reservations', 'commissions.reservation_id', '=', 'reservations.id')
                ->leftJoin('affiliate_links', 'reservations.affiliate_link_id', '=', 'affiliate_links.id')
                ->select([
                    'commissions.*',
                    'reservations.id as reservation_id',
                    'affiliate_links.generated_url as affiliate_link_url',
                ]);
        }
        if ($user->role_name === 'admin') {
            $query->join('users', 'commissions.affiliate_id', '=', 'users.id')
                ->leftJoin('reservations', 'commissions.reservation_id', '=', 'reservations.id')
                ->leftJoin('affiliate_links', 'reservations.affiliate_link_id', '=', 'affiliate_links.id')
                ->select([
                    'commissions.*',
                    'users.name as affiliate_name',
                    'reservations.id as reservation_id',
                    'affiliate_links.generated_url as affiliate_link_url',
                ]);
        }



        // Filtro por búsqueda en descripción
        if ($request->search) {
            $query->where('description', 'like', "%{$request->search}%", );
            if ($user->role_name === 'admin') {
                $query->orWhere('users.name', 'like', "%{$request->search}%");
            }
        }

        if ($request->date_from) {
            $query->whereDate('commissions.generated_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('commissions.generated_at', '<=', $request->date_to);
        }

        if ($request->filled('order_by')) {
            $query->orderBy($request->order_by, $request->order_dir ?? 'asc');
        } else {
            $query->orderByDesc('generated_at'); // orden por defecto
        }
        if ($request->filled('status')) {
            $query->where('commissions.status', $request->status);
        }

        $comisions = $query->paginate(10)->appends($request->all());
        return Inertia::render('Comisions', [
            'comisions' => $comisions,
            'filters' => [
                'search' => $request->search,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'affiliate_id' => $request->affiliate_id,
                'order_by' => $request->order_by,
                'order_dir' => $request->order_dir,
                'status' => $request->status,
                'is_paid' => $request->is_paid,
                'page' => $request->page ?? 1,
            ],
            'pagination' => [
                'current_page' => $comisions->currentPage(),
                'last_page' => $comisions->lastPage(),
            ],
        ]);
    }

    public function Links(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'No tienes permiso para ver esta página.');
        }

        $query = Link::query()
            ->join('rental_properties as property', 'affiliate_links.property_id', '=', 'property.id')
            ->select('affiliate_links.*', 'property.title as property_title')
            ->selectSub(function ($q) {
                $q->from('commissions')
                    ->join('reservations', 'commissions.reservation_id', '=', 'reservations.id')
                    ->whereColumn('reservations.affiliate_link_id', 'affiliate_links.id')
                    ->selectRaw('SUM(commissions.amount)');
            }, 'total_earned');

        if ($user->role_name === 'affiliate') {
            $query->where('affiliate_id', $user->id);
        }

        if ($user->role_name === 'admin') {
            $query->join('users', 'affiliate_links.affiliate_id', '=', 'users.id')
                ->addSelect('affiliate_links.*', 'users.name as affiliate_name');
        }


        if ($request->search) {
            $query->where('affiliate_links.generated_url', 'like', "%{$request->search}%");
        }

        if ($request->date_from) {
            $query->whereDate('affiliate_links.created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('affiliate_links.created_at', '<=', $request->date_to);
        }

        if ($request->affiliate_id) {
            $query->where('affiliate_links.affiliate_id', $request->affiliate_id);
        }


        if (in_array($request->order_by, ['clicks', 'conversions'])) {
            $query->orderBy($request->order_by, $request->order_dir === 'asc' ? 'asc' : 'desc');
        }



        $links = $query->paginate(10)->appends($request->all());

        return Inertia::render('Links', [
            'links' => $links->through(fn($link) => [
                'id' => $link->id,
                'property_title' => $link->property_title,
                'generated_url' => $link->generated_url,
                'clicks' => $link->clicks,
                'conversions' => $link->conversions,
                'created_at' => $link->created_at,
                'affiliate_name' => $link->affiliate?->name,
                'total_earned' => number_format($link->total_earned ?? 0, 2),
            ]),
            'filtersLink' => $request->only(['search', 'date', 'order_by', 'order_dir', 'page']),
        ]);

    }
    public function exportComisions(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $query = Commission::query();

        if ($user->role_name === 'affiliate') {
            $query->where('affiliate_id', $user->id);
        }

        if ($user->role_name === 'admin') {
            $query->join('users', 'commissions.affiliate_id', '=', 'users.id')
                ->leftJoin('reservations', 'commissions.reservation_id', '=', 'reservations.id')
                ->leftJoin('affiliate_links', 'reservations.affiliate_link_id', '=', 'affiliate_links.id')
                ->select([
                    'commissions.*',
                    'users.name as affiliate_name',
                    'reservations.id as reservation_id',
                    'affiliate_links.generated_url as affiliate_link_url',
                ]);
        }

        // Filtros reutilizados
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request, $user) {
                $q->where('commissions.description', 'like', '%' . $request->search . '%');
                if ($user->role_name === 'admin') {
                    $q->orWhere('users.name', 'like', '%' . $request->search . '%');
                }
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('commissions.generated_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('commissions.generated_at', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('commissions.status', $request->status);
        }

        if ($request->filled('is_paid')) {
            $query->where('commissions.is_paid', $request->is_paid);
        }

        if ($request->filled('affiliate_id') && $user->role_name === 'admin') {
            $query->where('commissions.affiliate_id', $request->affiliate_id);
        }

        $filename = 'comisiones_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = [
            'affiliate_name' => 'Afiliado',
            'description' => 'Descripción',
            'amount' => 'Cantidad (€)',
            'status' => 'Estado',
            'is_paid' => 'Pagado',
            'paid_at' => 'Fecha de pago',
            'generated_at' => 'Fecha generada',
            'reservation_id' => 'Reserva',
            'affiliate_link_url' => 'Link afiliado',
        ];

        return Response::stream(function () use ($query, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, array_values($columns));

            $query->chunk(100, function ($coms) use ($handle, $columns) {
                foreach ($coms as $row) {
                    $data = [];
                    foreach (array_keys($columns) as $key) {
                        $value = $row->{$key} ?? '—';
                        if ($key === 'is_paid') {
                            $value = $row->is_paid ? 'Sí' : 'No';
                        }
                        if ($key === 'amount') {
                            $value = number_format($row->amount, 2);
                        }
                        $data[] = $value;
                    }
                    fputcsv($handle, $data);
                }
            });

            fclose($handle);
        }, 200, $headers);

    }
    public function exportLink(Request $request)
    {
        $query = Link::query()->with('affiliate');

        if ($request->search) {
            $query->where('generated_url', 'like', "%{$request->search}%");
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->affiliate_id) {
            $query->where('affiliate_id', $request->affiliate_id);
        }

        $links = $query->get();

        if ($request->export === 'csv') {
            return response()->streamDownload(function () use ($links) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Afiliado', 'URL Padre', 'URL Generada', 'Clicks', 'Conversiones', 'Fecha']);
                foreach ($links as $link) {
                    fputcsv($handle, [
                        $link->affiliate->name ?? '',
                        $link->target_url,
                        $link->generated_url,
                        $link->clicks,
                        $link->conversions,
                        $link->created_at->format('d/m/Y'),
                    ]);
                }
                fclose($handle);
            }, 'links.csv');
        }

        abort(400, 'Formato no válido');
    }

    public function createLink()
    {
        $user = Auth::user();
        if (!$user || $user->role_name !== 'affiliate') {
            abort(403, 'No tienes permiso para crear enlaces.');
        }

        $properties = Property::all();

        return Inertia::render('SelectorPropietats', [
            'houses' => $properties,
        ]);
    }

    public function storeLink(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role_name !== 'affiliate') {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate([
            'property_id' => 'required|exists:rental_properties,id',
            'name' => 'nullable|string|max:100',
        ]);

        // Obtener la propiedad para generar la URL base
        $property = Property::findOrFail($validated['property_id']);

        // Crear un token único o hash para el link
        $token = Str::random(10);

        $generatedUrl = url("/property/{$property->id}?ref={$user->id}-{$token}");

        $link = Link::create([
            'affiliate_id' => $user->id,
            'property_id' => $property->id,
            'target_url' => url("/property/{$property->id}"),
            'generated_url' => $generatedUrl,
            'clicks' => 0,
            'conversions' => 0,
            'name' => $validated['name'] ?? null,
        ]);

        return redirect()->route('links')->with('success', 'Enlace de afiliado creado correctamente.');
    }

}

