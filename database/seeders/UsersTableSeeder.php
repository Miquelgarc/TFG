<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $affiliateRoleId = DB::table('roles')->where('name', 'affiliate')->value('id');
        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        DB::table('users')->insert([
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'status' => 'active',
                'role_id' => $adminRoleId,
            ],
            [
                'name' => 'Affiliate Two',
                'email' => 'affiliate2@example.com',
                'password' => Hash::make('password'),
                'company_name' => 'ClickBoost',
                'website_url' => 'https://clickboost.net',
                'status' => 'pending',
                'role_id' => $affiliateRoleId,
                'remember_token' => \Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
