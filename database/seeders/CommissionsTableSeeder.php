<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CommissionsTableSeeder extends Seeder
{
    public function run(): void
    {
        $affiliate1 = DB::table('users')->where('email', 'affiliate1@example.com')->first();
        $affiliate2 = DB::table('users')->where('email', 'affiliate2@example.com')->first();

        $properties = DB::table('rental_properties')->take(10)->get();

        if (!$affiliate1 || !$affiliate2 || $properties->isEmpty()) {
            $this->command->warn('No hay suficientes usuarios o propiedades para generar comisiones.');
            return;
        }

        $types = ['click', 'conversion', 'booking'];
        $statuses = ['pending', 'approved', 'paid'];

        $commissions = [];

        for ($i = 0; $i < 22; $i++) {
            $isAffiliate1 = $i % 2 === 0;
            $affiliate = $isAffiliate1 ? $affiliate1 : $affiliate2;
            $property = $properties[$i % $properties->count()];

            $status = $statuses[array_rand($statuses)];
            $paidAt = $status === 'paid' ? now()->subDays(rand(0, 5)) : null;

            $commissions[] = [
                'affiliate_id' => $affiliate->id,
                'reservation_id' => rand(1, 10), // Asumiendo que hay al menos 50 reservas
                'amount' => round(rand(1500, 6000) / 100, 2),
                'description' => 'Commission for Property #' . $property->id,
                'status' => $status,
                'paid_at' => $paidAt,
                'generated_at' => now()->subDays(rand(1, 20)),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('commissions')->insert($commissions);
    }
}
