<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Role;
use App\Models\Comisions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


class AfiliatController extends Controller
{
    // InfoAfiliatController.php
    public function index()
    {
        $user = auth()->user();


        if ($user->is_admin()) {
            return redirect()->route('afiliats');
        } elseif (!$user->is_admin()) {
            
            $comisionesSemanal = Comisions::selectRaw("
    YEARWEEK(generated_at) as semana,
    CONCAT('Semana ', WEEK(generated_at), ' - ', YEAR(generated_at)) as semana_nombre,
    SUM(amount) as total
")->where('affiliate_id', $user->id)
                ->groupBy('semana', 'semana_nombre')
                ->orderBy('semana')
                ->get();


            return Inertia::render('InfoAfiliat', [
                'auth' => [
                    'user' => $user,
                ],
                'comisionesSemanales' => $comisionesSemanal,
            ]);
        }
    }

}
