<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Comisions;
use App\Models\Link;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;


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

        $query->join('users', 'commissions.affiliate_id', '=', 'users.id')
            ->select('commissions.*', 'users.name as affiliate_name');


        // Filtro por búsqueda en descripción
        if ($search = $request->input('search')) {
            $query->where('description', 'like', "%{$search}%", )
                ->orWhere('users.name', 'like', "%{$search}%");
        }

        // Filtro por fecha (opcional)
        if ($date = $request->input('date')) {
            $query->whereDate('commissions.created_at', $date);
        }

        $comisions = $query->with('afiliat')->orderByDesc('created_at')->paginate(12)->withQueryString();

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

        $query->join('users', 'affiliate_links.affiliate_id', '=', 'users.id')
            ->select('affiliate_links.*', 'users.name as affiliate_name');

        if ($request->search) {
            $query->where('description', 'like', "%{$request->search}%")
                ->orWhere('users.name', 'like', "%{$request->search}%");
        }

        if ($request->date) {
            $query->whereDate('affiliate_links.created_at', $request->date);
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
}

