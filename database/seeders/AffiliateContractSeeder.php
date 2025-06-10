<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AffiliateContract;
use App\Models\AffiliateLevel;
use App\Models\User;

class AffiliateContractSeeder extends Seeder
{
    public function run(): void
    {
        $level = AffiliateLevel::where('name', 'Bronze')->first();

        // Aplica el contrato "Bronze" a todos los afiliados por defecto
        $affiliates = User::where('role_id', '2')->get();

        foreach ($affiliates as $user) {
            AffiliateContract::updateOrCreate([
                'affiliate_id' => $user->id,
            ], [
                'affiliate_level_id' => $level->id,
                'starts_at' => now()->subMonths(1),
                'ends_at' => null, // contrato indefinido
            ]);
        }
    }
}
