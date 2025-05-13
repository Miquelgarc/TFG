<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'admin', 'display_name' => 'Administrator', 'description' => 'Full access to the system'],
            ['name' => 'affiliate', 'display_name' => 'Affiliate', 'description' => 'Affiliate user'],
        ]);
    }
}
