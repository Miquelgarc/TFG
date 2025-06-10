<?php

namespace App\Http\Controllers;

use App\Models\AffiliateLink;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Role;
use App\Models\Commission;
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
                ->select('al.generated_url', 'al.name', DB::raw('COUNT(r.id) as total_reservas'))
                ->where('al.affiliate_id', $user->id)
                ->groupBy('al.generated_url', 'al.name')
                ->orderByDesc('total_reservas')
                ->limit(5)
                ->get();


            $comisionesSemanal = DB::table('commissions')
                ->selectRaw("
        YEARWEEK(generated_at, 1) AS semana,
        WEEK(generated_at, 1) AS semana_num,
        YEAR(generated_at) AS anio,
        ROUND(SUM(amount), 2) AS total
    ")
                ->where('affiliate_id', $user->id)
                ->groupBy('semana', 'semana_num', 'anio')
                ->orderBy('semana')
                ->get()
                ->map(function ($item) {
                    $item->semana_nombre = 'Semana ' . $item->semana_num . ' - ' . $item->anio;
                    return $item;
                });
            $totalComisiones = Commission::where('affiliate_id', $user->id)
                ->where('status', 'paid')
                ->sum('amount');

            $comissionsPendents = Commission::where('affiliate_id', $user->id)
                ->where('status', 'approved')
                ->sum('amount');
            $totalClicks = DB::table('affiliate_links')
                ->where('affiliate_links.affiliate_id', $user->id)
                ->sum('affiliate_links.clicks');


            $totalReservas = DB::table('reservations')
                ->join('affiliate_links', 'reservations.affiliate_link_id', '=', 'affiliate_links.id')
                ->where('affiliate_links.affiliate_id', $user->id)
                ->where('reservations.status', 'confirmed')
                ->count();

            $nivel = $user->currentAffiliateContract?->level?->name ?? 'Sense nivell';
            $porcentaje = $user->currentAffiliateContract?->level?->commission_percentage ?? 0;


            return Inertia::render('InfoAfiliat', [
                'auth' => [
                    'user' => $user,
                ],
                'comisionesSemanales' => $comisionesSemanal,
                'linksTop' => $topAffiliateLinks,
                'stats' => [
                    'nivel' => $nivel,
                    'porcentaje' => $porcentaje,
                    'clicks' => $totalClicks,
                    'reservas' => $totalReservas,
                    'total_comisiones' => number_format($totalComisiones, 2),
                    'comissions_pendents' => number_format($comissionsPendents, 2),
                ],
            ]);
        }
    }

}
