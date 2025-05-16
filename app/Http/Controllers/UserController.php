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

        if ($user->role_name === 'afiliat') {
            $query->where('afiliat_id', $user->id);
        }

        // Filtro por búsqueda en descripción
        if ($search = $request->input('search')) {
            $query->where('description', 'like', "%{$search}%");
        }

        // Filtro por fecha (opcional)
        if ($date = $request->input('date')) {
            $query->whereDate('generated_at', $date);
        }

        $comisions = $query->with('afiliat')->orderByDesc('generated_at')->paginate(12)->withQueryString();

        return Inertia::render('Comisions', [
            'comisions' => $comisions,
            'filters' => [
                'search' => $request->search,
                'date' => $request->date,
            ],
            'user' => $user,
        ]);
    }

    public function Links(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'No tienes permiso para ver esta página.');
        }

        $query = Link::query();

        if ($user->role_name === 'afiliat') {
            $query->where('id', $user->id);
        }

        // Filtro por búsqueda en descripción
        if ($search = $request->input('search')) {
            $query->where('link', 'like', "%{$search}%");
        }

        // Filtro por fecha (opcional)
        if ($date = $request->input('date')) {
            $query->whereDate('created_at', $date);
        }

        $links = $query->with('user')->orderByDesc('created_at')->paginate(10)->withQueryString();

        return Inertia::render('Links', [
            'links' => $links,
            'filters' => [
                'search' => $request->search,
                'date' => $request->date,
            ],
            'user' => $user,
        ]);
    }

    
}

