<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AffiliateLevel;

class AffiliateLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['name' => 'Bronze', 'commission_percentage' => 5, 'min_reservations' => 0, 'min_clicks' => 0],
            ['name' => 'Silver', 'commission_percentage' => 8, 'min_reservations' => 30, 'min_clicks' => 50],
            ['name' => 'Gold', 'commission_percentage' => 10, 'min_reservations' => 100, 'min_clicks' => 2000],
            ['name' => 'Platinum', 'commission_percentage' => 12, 'min_reservations' => 500, 'min_clicks' => 10000],
        ];

        foreach ($levels as $level) {
            AffiliateLevel::updateOrCreate(['name' => $level['name']], $level);
        }
    }
}
