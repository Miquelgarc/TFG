<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\AffiliateLevel;
use App\Models\AffiliateContract;
use App\Models\AffiliateClick;
use App\Models\Reservation;
use App\Models\Comisions;
use Carbon\Carbon;

class UpdateAffiliateLevels extends Command
{
    protected $signature = 'affiliates:update-levels';
    protected $description = 'Actualiza automáticamente el nivel de cada afiliado según su rendimiento';

    public function handle()
    {
        $this->info("Iniciando actualización de niveles...");

        $affiliates = User::whereHas('role', fn($q) => $q->where('name', 'afiliado'))->get();

        foreach ($affiliates as $affiliate) {
            $clicks = AffiliateClick::whereHas('affiliateLink', fn($q) =>
                $q->where('affiliate_id', $affiliate->id)
            )->count();

            $reservations = Reservation::whereHas('affiliateLink', fn($q) =>
                $q->where('affiliate_id', $affiliate->id)
            )->where('status', 'confirmed')->count();

            $earnings = Comisions::where('affiliate_id', $affiliate->id)->sum('amount');

            $level = AffiliateLevel::query()
                ->where(function ($q) use ($clicks, $reservations, $earnings) {
                    $q->where('min_clicks', '<=', $clicks)
                      ->orWhere('min_reservations', '<=', $reservations)
                      ->orWhere('min_total_earnings', '<=', $earnings);
                })
                ->orderByDesc('commission_percentage')
                ->first();

            if (!$level) {
                $this->warn("No se encontró un nivel adecuado para el afiliado ID {$affiliate->id}");
                continue;
            }

            $currentContract = AffiliateContract::where('affiliate_id', $affiliate->id)
                ->latest('starts_at')
                ->first();

            if (!$currentContract || $currentContract->affiliate_level_id !== $level->id) {
                if ($currentContract) {
                    $currentContract->update(['ends_at' => Carbon::now()]);
                }

                AffiliateContract::create([
                    'affiliate_id' => $affiliate->id,
                    'affiliate_level_id' => $level->id,
                    'starts_at' => now(),
                ]);

                $this->info("Afiliado ID {$affiliate->id} actualizado al nivel '{$level->name}'");
            }
        }

        $this->info("Actualización de niveles completada.");
    }
}
