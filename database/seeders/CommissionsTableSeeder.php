<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommissionsTableSeeder extends Seeder
{
    public function run(): void
    {
        $affiliate1 = DB::table('users')->where('email', 'affiliate1@example.com')->first();

        DB::table('commissions')->insert([
            [
                'affiliate_id' => $affiliate1->id,
                'amount' => 25.50,
                'description' => 'Commission for Product A',
                'generated_at' => now()->subDays(3),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'affiliate_id' => $affiliate1->id,
                'amount' => 15.00,
                'description' => 'Commission for Product B',
                'generated_at' => now()->subDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
