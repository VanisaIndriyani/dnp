<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DivisionUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');
        
        $divisions = [
            'cover' => [
                ['name' => 'Budi Santoso', 'nik' => '10010001'],
                ['name' => 'Siti Aminah', 'nik' => '10010002'],
                ['name' => 'Rahmat Hidayat', 'nik' => '10010003'],
                ['name' => 'Dewi Lestari', 'nik' => '10010004'],
                ['name' => 'Agus Pratama', 'nik' => '10010005'],
            ],
            'case' => [
                ['name' => 'Eko Kurniawan', 'nik' => '20020001'],
                ['name' => 'Fajar Nugraha', 'nik' => '20020002'],
                ['name' => 'Gita Pertiwi', 'nik' => '20020003'],
                ['name' => 'Hendra Wijaya', 'nik' => '20020004'],
                ['name' => 'Indah Sari', 'nik' => '20020005'],
            ],
            'inner' => [
                ['name' => 'Joko Susilo', 'nik' => '30030001'],
                ['name' => 'Kartika Putri', 'nik' => '30030002'],
                ['name' => 'Lukman Hakim', 'nik' => '30030003'],
                ['name' => 'Maya Anggraini', 'nik' => '30030004'],
                ['name' => 'Nanda Saputra', 'nik' => '30030005'],
            ],
            'endplate' => [
                ['name' => 'Oscar Mahendra', 'nik' => '40040001'],
                ['name' => 'Putri Wulandari', 'nik' => '40040002'],
                ['name' => 'Qori Handayani', 'nik' => '40040003'],
                ['name' => 'Rizal Firmansyah', 'nik' => '40040004'],
                ['name' => 'Sari Indah', 'nik' => '40040005'],
            ],
        ];

        foreach ($divisions as $division => $users) {
            foreach ($users as $userData) {
                // Check if user exists to avoid duplicates
                if (!User::where('nik', $userData['nik'])->exists()) {
                    User::create([
                        'name' => $userData['name'],
                        'nik' => $userData['nik'],
                        'role' => 'operator',
                        'division' => $division,
                        'status' => 'active',
                        'password' => $password,
                    ]);
                }
            }
        }
    }
}
