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
                'company_name' => '',
                'website_url' => '',
                'status' => 'active',
                'role_id' => $adminRoleId,
                'remember_token' => \Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Affiliate One',
                'email' => 'affiliate1@example.com',
                'password' => Hash::make('password'),
                'company_name' => 'Affiliate Company',
                'website_url' => 'https://affiliatecompany.com',
                'status' => 'active',
                'role_id' => $affiliateRoleId,
                'remember_token' => \Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Affiliate Two',
                'email' => 'affiliate2@example.com',
                'password' => Hash::make('password'),
                'company_name' => 'Affiliate Company',
                'website_url' => 'https://affiliatecompany.com',
                'status' => 'active',
                'role_id' => $affiliateRoleId,
                'remember_token' => \Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
            'name' => 'Affiliate Three',
            'email' => 'affiliate3@example.com',
            'password' => Hash::make('password'),
            'company_name' => 'NinjaFiliate',
            'website_url' => 'https://ninjafiliate.net',
            'status' => 'pending',
            'role_id' => $affiliateRoleId,
            'remember_token' => \Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'name' => 'Affiliate Four',
            'email' => 'affiliate4@example.com',
            'password' => Hash::make('password'),
            'company_name' => 'NatFiliate',
            'website_url' => 'https://natFiliate.net',
            'status' => 'pending',
            'role_id' => $affiliateRoleId,
            'remember_token' => \Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'name' => 'Affiliate Five',
            'email' => 'affiliate5@example.com',
            'password' => Hash::make('password'),
            'company_name' => 'Filiat',
            'website_url' => 'https://filiat.net',
            'status' => 'pending',
            'role_id' => $affiliateRoleId,
            'remember_token' => \Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'name' => 'Affiliate Six',
            'email' => 'affiliate6@example.com',
            'password' => Hash::make('password'),
            'company_name' => 'notap',
            'website_url' => 'https://notap.net',
            'status' => 'pending',
            'role_id' => $affiliateRoleId,
            'remember_token' => \Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'name' => 'Affiliate Seven',
            'email' => 'affiliate7@example.com',
            'password' => Hash::make('password'),
            'company_name' => 'Affilify',
            'website_url' => 'https://affilify.net',
            'status' => 'pending',
            'role_id' => $affiliateRoleId,
            'remember_token' => \Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'name' => 'Affiliate Eight',
            'email' => 'affiliate8@example.com',
            'password' => Hash::make('password'),
            'company_name' => 'Affilink',
            'website_url' => 'https://affilink.net',
            'status' => 'pending',
            'role_id' => $affiliateRoleId,
            'remember_token' => \Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'name' => 'Affiliate Nine',
            'email' => 'affiliate9@example.com',
            'password' => Hash::make('password'),
            'company_name' => 'LinkFiliate',
            'website_url' => 'https://linkfiliate.net',
            'status' => 'pending',
            'role_id' => $affiliateRoleId,
            'remember_token' => \Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'name' => 'Affiliate Ten',
            'email' => 'affiliate10@example.com',
            'password' => Hash::make('password'),
            'company_name' => 'FiliateX',
            'website_url' => 'https://filiatex.net',
            'status' => 'pending',
            'role_id' => $affiliateRoleId,
            'remember_token' => \Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
            ],
        
        ]);
    }
}
