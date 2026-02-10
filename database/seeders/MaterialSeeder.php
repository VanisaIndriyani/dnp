<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Material;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materials = [
            [
                'title' => 'Panduan Keselamatan Kerja (K3)',
                'category' => 'Safety',
                'file_path' => 'materials/panduan_k3.pdf',
            ],
            [
                'title' => 'SOP Pengoperasian Mesin Press',
                'category' => 'SOP',
                'file_path' => 'materials/sop_mesin_press.pdf',
            ],
            [
                'title' => 'Teknik Dasar Perawatan Filter',
                'category' => 'Technical',
                'file_path' => 'materials/maintenance_filter.pdf',
            ],
            [
                'title' => 'Peraturan Perusahaan 2026',
                'category' => 'General',
                'file_path' => 'materials/peraturan_perusahaan.pdf',
            ],
            [
                'title' => 'Prosedur Penanganan Limbah B3',
                'category' => 'Safety',
                'file_path' => 'materials/limbah_b3.pdf',
            ],
        ];

        foreach ($materials as $material) {
            Material::create($material);
        }
    }
}
