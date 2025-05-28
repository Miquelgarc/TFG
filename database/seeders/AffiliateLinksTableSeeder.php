<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AffiliateLinksTableSeeder extends Seeder
{
    public function run(): void
    {
        $affiliate1 = DB::table('users')->where('email', 'affiliate1@example.com')->first();
        $affiliate2 = DB::table('users')->where('email', 'affiliate2@example.com')->first();

        $property1 = DB::table('rental_properties')->first();
        $property2 = DB::table('rental_properties')->skip(1)->first();

        if (!$affiliate1 || !$affiliate2 || !$property1 || !$property2) {
            $this->command->warn('No hay suficientes usuarios o propiedades para generar enlaces de afiliados.');
            return;
        }

        $now = now();

        DB::table('affiliate_links')->insert([
            [
                'affiliate_id'    => $affiliate1->id,
                'property_id'     => $property1->id,
                'target_url'      => 'https://yourapp.com/properties/' . $property1->id . '?utm_source=affiliate1&utm_medium=link',
                'generated_url'   => 'https://yourapp.com/ref/' . Str::random(8),
                'clicks'          => 10,
                'conversions'     => 2,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'affiliate_id'    => $affiliate2->id,
                'property_id'     => $property2->id,
                'target_url'      => 'https://yourapp.com/properties/' . $property2->id . '?utm_source=affiliate2&utm_medium=link',
                'generated_url'   => 'https://yourapp.com/ref/' . Str::random(8),
                'clicks'          => 5,
                'conversions'     => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'affiliate_id'    => $affiliate1->id,
                'property_id'     => $property2->id,
                'target_url'      => 'https://yourapp.com/properties/' . $property2->id . '?utm_source=affiliate1&utm_medium=banner',
                'generated_url'   => 'https://yourapp.com/ref/' . Str::random(8),
                'clicks'          => 25,
                'conversions'     => 7,
                'created_at'      => $now->subDays(3),
                'updated_at'      => $now->subDays(1),
            ],
        ]);
    }
}
