<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AffiliateLinksTableSeeder extends Seeder
{
    public function run(): void
    {
        $affiliate1 = DB::table('users')->where('email', 'affiliate1@example.com')->first();
        $affiliate2 = DB::table('users')->where('email', 'affiliate2@example.com')->first();

        DB::table('affiliate_links')->insert([
            [
                'affiliate_id' => $affiliate1->id,
                'target_url' => 'https://yourstore.com/product/1',
                'generated_url' => 'https://yourstore.com/ref/aff1prod1',
                'clicks' => 10,
                'conversions' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'affiliate_id' => $affiliate2->id,
                'target_url' => 'https://yourstore.com/product/2',
                'generated_url' => 'https://yourstore.com/ref/aff2prod2',
                'clicks' => 5,
                'conversions' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
