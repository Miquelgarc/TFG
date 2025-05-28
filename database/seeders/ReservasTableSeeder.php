<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Property;
use App\Models\AffiliateLink;
use Illuminate\Support\Carbon;

class ReservasTableSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::inRandomOrder()->take(5)->get();
        $properties = Property::inRandomOrder()->take(5)->get();
        $affiliateLinks = AffiliateLink::inRandomOrder()->take(3)->get();

        $statuses = ['pending', 'confirmed', 'cancelled', 'charged'];

        foreach (range(1, 10) as $i) {
            $checkIn = Carbon::today()->addDays(rand(1, 30));
            $checkOut = (clone $checkIn)->addDays(rand(2, 10));

            Reservation::create([
                'property_id' => $properties->random()->id,
                'user_id' => $users->random()->id,
                'affiliate_link_id' => rand(0, 1) ? $affiliateLinks->random()->id : null,
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'total_price' => rand(300, 2000),
                'status' => $statuses[array_rand($statuses)],
            ]);
        }
    }
}
