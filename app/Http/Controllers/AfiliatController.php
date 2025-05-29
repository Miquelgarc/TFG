<?php

namespace App\Http\Controllers;

use App\Models\AffiliateLink;
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
            $topAffiliateLinks = DB::table('reservations as r')
                ->join('affiliate_links as al', 'r.affiliate_link_id', '=', 'al.id')
                ->select('al.generated_url', DB::raw('COUNT(r.id) as total_reservas'))
                ->where('al.affiliate_id', $user->id)
                ->groupBy('al.generated_url')
                ->orderByDesc('total_reservas')
                ->limit(5)
                ->get();


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
                'linksTop' => $topAffiliateLinks,
            ]);
        }
    }

}
