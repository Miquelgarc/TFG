<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Comisions;
use App\Models\Link;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Exports\LinksExport;
use Maatwebsite\Excel\Facades\Excel;


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

        $query = User::query()->where('role_id', 2);

        // Filtro por búsqueda
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        // Filtro por estado, evitando aplicar si es null o string vacío
        $status = $request->input('status');
        if (!is_null($status) && $status !== '') {
            $query->where('status', $status);
        }

        $afiliates = $query->paginate(10)->withQueryString();

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

        $query = Comisions::query();

        if ($user->role_name === 'affiliate') {
            $query->where('affiliate_id', $user->id);
        }
        if ($user->role_name === 'admin') {
            $query->join('users', 'commissions.affiliate_id', '=', 'users.id')
                ->select('commissions.*', 'users.name as affiliate_name');
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
        $comisions = $query->paginate(10)->appends($request->all());
        return Inertia::render('Comisions', [
            'comisions' => $comisions,
            'filters' => [
                'search' => $request->search,
                'date' => $request->date,
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

        $query = Link::query();

        if ($user->role_name === 'affiliate') {
            $query->where('affiliate_id', $user->id);
        }

        if ($user->role_name === 'admin') {
            $query->join('users', 'affiliate_links.affiliate_id', '=', 'users.id')
            ->select('affiliate_links.*', 'users.name as affiliate_name');
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
            'links' => $links,
            'filtersLink' => $request->only(['search', 'date', 'order_by', 'order_dir', 'page']),
        ]);

    }
    public function exportComisions(Request $request)
    {
        $query = Comisions::query();

        if ($request->search) {
            $query->where('description', 'like', "%{$request->search}%");
        }

        if ($request->date) {
            $query->whereDate('generated_at', $request->date);
        }

        if (Auth::user()->role_name === 'affiliate') {
            $query->where('affiliate_id', Auth::id());
        }

        $comisions = $query->with('afiliat')->get();

        if ($request->export === 'csv') {
            return response()->streamDownload(function () use ($comisions) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Afiliado', 'Descripción', 'Importe', 'Fecha']);
                foreach ($comisions as $comision) {
                    fputcsv($handle, [
                        $comision->afiliat->name ?? '',
                        $comision->description,
                        number_format($comision->amount, 2, ',', '.'),
                        $comision->generated_at->format('d/m/Y'),
                    ]);
                }
                fclose($handle);
            }, 'comisions.csv');
        }

        abort(400, 'Formato no válido');

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

}

