<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'nik' => '99999999',
            'role' => 'super_admin',
            'status' => 'active',
            'password' => bcrypt('password'), // Ganti dengan password yang aman nanti
        ]);

        // Admin
        User::create([
            'name' => 'Admin Produksi',
            'nik' => '88888888',
            'role' => 'admin',
            'status' => 'active',
            'password' => bcrypt('password'),
        ]);

        // Operator
        User::create([
            'name' => 'Operator Case',
            'nik' => '12345678',
            'role' => 'operator',
            'division' => 'case',
            'status' => 'active',
            'password' => bcrypt('password'),
        ]);

        $this->call([
           
            EvaluationSeeder::class,
        ]);
    }
}
