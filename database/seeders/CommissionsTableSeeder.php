<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommissionsTableSeeder extends Seeder
{
    public function run(): void
    {
        $affiliate1 = DB::table('users')->where('email', 'affiliate1@example.com')->first();
        $affiliate2 = DB::table('users')->where('email', 'affiliate2@example.com')->first();
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
            [
                'affiliate_id' => $affiliate1->id,
                'amount' => 30.00,
                'description' => 'Commission for Product C',
                'generated_at' => now()->subDays(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'affiliate_id' => $affiliate1->id,
                'amount' => 45.75,
                'description' => 'Commission for Product D',
                'generated_at' => now()->subDays(8),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'affiliate_id' => $affiliate1->id,
                'amount' => 20.00,
                'description' => 'Commission for Product E',
                'generated_at' => now()->subDays(6),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'affiliate_id' => $affiliate1->id,
                'amount' => 50.00,
                'description' => 'Commission for Product F',
                'generated_at' => now()->subDays(4),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'affiliate_id' => $affiliate1->id,
                'amount' => 35.00,
                'description' => 'Commission for Product G',
                'generated_at' => now()->subDays(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'affiliate_id' => $affiliate2->id,
                'amount' => 40.00,
                'description' => 'Commission for Product H',
                'generated_at' => now()->subDays(9),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'affiliate_id' => $affiliate2->id,
                'amount' => 25.00,
                'description' => 'Commission for Product I',
                'generated_at' => now()->subDays(7),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'affiliate_id' => $affiliate2->id,
                'amount' => 60.00,
                'description' => 'Commission for Product J',
                'generated_at' => now()->subDays(5),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'affiliate_id' => $affiliate2->id,
                'amount' => 15.00,
                'description' => 'Commission for Product K',
                'generated_at' => now()->subDays(3),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'affiliate_id' => $affiliate2->id,
                'amount' => 55.00,
                'description' => 'Commission for Product L',
                'generated_at' => now()->subDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'affiliate_id' => $affiliate1->id,
                'amount' => 22.50,
                'description' => 'Commission for Product M',
                'generated_at' => now()->subDays(11),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'affiliate_id' => $affiliate1->id,
                'amount' => 18.75,
                'description' => 'Commission for Product N',
                'generated_at' => now()->subDays(12),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'affiliate_id' => $affiliate1->id,
                'amount' => 42.00,
                'description' => 'Commission for Product O',
                'generated_at' => now()->subDays(13),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'affiliate_id' => $affiliate2->id,
                'amount' => 33.33,
                'description' => 'Commission for Product P',
                'generated_at' => now()->subDays(14),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'affiliate_id' => $affiliate2->id,
                'amount' => 27.50,
                'description' => 'Commission for Product Q',
                'generated_at' => now()->subDays(15),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'affiliate_id' => $affiliate2->id,
                'amount' => 48.00,
                'description' => 'Commission for Product R',
                'generated_at' => now()->subDays(16),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'affiliate_id' => $affiliate1->id,
                'amount' => 19.99,
                'description' => 'Commission for Product S',
                'generated_at' => now()->subDays(17),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'affiliate_id' => $affiliate1->id,
                'amount' => 31.25,
                'description' => 'Commission for Product T',
                'generated_at' => now()->subDays(18),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'affiliate_id' => $affiliate2->id,
                'amount' => 29.99,
                'description' => 'Commission for Product U',
                'generated_at' => now()->subDays(19),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'affiliate_id' => $affiliate2->id,
                'amount' => 50.50,
                'description' => 'Commission for Product V',
                'generated_at' => now()->subDays(20),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
