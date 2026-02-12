<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Evaluation;

class EvaluationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            // COVER - Multiple Choice
            [
                'type' => 'multiple_choice',
                'category' => 'cover',
                'question' => 'Apa warna langit?',
                'option_a' => 'Merah',
                'option_b' => 'Biru',
                'option_c' => 'Hijau',
                'option_d' => 'Kuning',
                'correct_answer' => 'b',
            ],
            [
                'type' => 'multiple_choice',
                'category' => 'cover',
                'question' => 'Apa fungsi utama dari Cover pada produk?',
                'option_a' => 'Sebagai hiasan saja',
                'option_b' => 'Melindungi komponen bagian dalam',
                'option_c' => 'Menambah berat produk',
                'option_d' => 'Agar terlihat mahal',
                'correct_answer' => 'b',
            ],
            [
                'type' => 'multiple_choice',
                'category' => 'cover',
                'question' => 'Material apa yang umum digunakan untuk Cover?',
                'option_a' => 'Kertas Tisu',
                'option_b' => 'Plastik / Logam',
                'option_c' => 'Air',
                'option_d' => 'Kaca Tipis',
                'correct_answer' => 'b',
            ],
            [
                'type' => 'multiple_choice',
                'category' => 'cover',
                'question' => 'Cacat visual apa yang harus dihindari pada Cover?',
                'option_a' => 'Permukaan halus',
                'option_b' => 'Warna merata',
                'option_c' => 'Goresan (Scratch)',
                'option_d' => 'Tidak ada debu',
                'correct_answer' => 'c',
            ],
            [
                'type' => 'multiple_choice',
                'category' => 'cover',
                'question' => 'Alat ukur apa yang digunakan untuk mengecek dimensi Cover?',
                'option_a' => 'Timbangan',
                'option_b' => 'Caliper / Jangka Sorong',
                'option_c' => 'Thermometer',
                'option_d' => 'Stopwatch',
                'correct_answer' => 'b',
            ],
            [
                'type' => 'multiple_choice',
                'category' => 'cover',
                'question' => 'Standar kebersihan Cover sebelum dikirim adalah?',
                'option_a' => 'Boleh ada minyak sedikit',
                'option_b' => 'Bebas dari debu, minyak, dan kotoran',
                'option_c' => 'Harus basah',
                'option_d' => 'Berwarna pudar',
                'correct_answer' => 'b',
            ],
            // COVER - Essay
            [
                'type' => 'essay',
                'category' => 'cover',
                'question' => 'Jelaskan prosedur inspeksi visual pada bagian Cover!',
                'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'correct_answer' => null,
            ],
            [
                'type' => 'essay',
                'category' => 'cover',
                'question' => 'Sebutkan 3 jenis defect yang sering terjadi pada proses produksi Cover!',
                'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'correct_answer' => null,
            ],
            [
                'type' => 'essay',
                'category' => 'cover',
                'question' => 'Bagaimana cara penanganan material Cover yang reject?',
                'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'correct_answer' => null,
            ],
            [
                'type' => 'essay',
                'category' => 'cover',
                'question' => 'Mengapa presisi dimensi sangat penting pada bagian Cover?',
                'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'correct_answer' => null,
            ],
            [
                'type' => 'essay',
                'category' => 'cover',
                'question' => 'Jelaskan langkah-langkah safety saat mengoperasikan mesin cetak Cover!',
                'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'correct_answer' => null,
            ],

            // CASE - Multiple Choice
            [
                'type' => 'multiple_choice',
                'category' => 'case',
                'question' => 'Fungsi utama Case adalah?',
                'option_a' => 'Wadah utama komponen',
                'option_b' => 'Aksesoris tambahan',
                'option_c' => 'Pemberat',
                'option_d' => 'Pendingin',
                'correct_answer' => 'a',
            ],
            [
                'type' => 'multiple_choice',
                'category' => 'case',
                'question' => 'Bagian Case biasanya terbuat dari?',
                'option_a' => 'Karet Gelang',
                'option_b' => 'Aluminium / Baja',
                'option_c' => 'Kertas Karton',
                'option_d' => 'Kain',
                'correct_answer' => 'b',
            ],
            [
                'type' => 'multiple_choice',
                'category' => 'case',
                'question' => 'Parameter penting dalam pengecekan Case adalah?',
                'option_a' => 'Rasa',
                'option_b' => 'Ketebalan dan Diameter',
                'option_c' => 'Bau',
                'option_d' => 'Suara',
                'correct_answer' => 'b',
            ],
            [
                'type' => 'multiple_choice',
                'category' => 'case',
                'question' => 'Jika Case penyok, apa dampaknya?',
                'option_a' => 'Tidak bisa dirakit (Assembly fail)',
                'option_b' => 'Lebih aerodinamis',
                'option_c' => 'Lebih ringan',
                'option_d' => 'Tidak ada dampak',
                'correct_answer' => 'a',
            ],
            [
                'type' => 'multiple_choice',
                'category' => 'case',
                'question' => 'Proses finishing pada Case bertujuan untuk?',
                'option_a' => 'Membuat kasar',
                'option_b' => 'Mencegah korosi dan memperhalus permukaan',
                'option_c' => 'Menambah cacat',
                'option_d' => 'Mengurangi kekuatan',
                'correct_answer' => 'b',
            ],
            // CASE - Essay
            [
                'type' => 'essay',
                'category' => 'case',
                'question' => 'Jelaskan alur proses produksi pembuatan Case dari bahan mentah sampai jadi!',
                'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'correct_answer' => null,
            ],
            [
                'type' => 'essay',
                'category' => 'case',
                'question' => 'Apa yang dimaksud dengan deburring pada proses Case?',
                'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'correct_answer' => null,
            ],
            [
                'type' => 'essay',
                'category' => 'case',
                'question' => 'Sebutkan potensi bahaya di area kerja Case!',
                'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'correct_answer' => null,
            ],
            [
                'type' => 'essay',
                'category' => 'case',
                'question' => 'Bagaimana cara memastikan Case tidak bocor (Leak Test)?',
                'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'correct_answer' => null,
            ],
            [
                'type' => 'essay',
                'category' => 'case',
                'question' => 'Jelaskan standar packing untuk part Case agar tidak rusak saat pengiriman!',
                'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'correct_answer' => null,
            ],

            // INNER - Multiple Choice
            [
                'type' => 'multiple_choice',
                'category' => 'inner',
                'question' => 'Komponen Inner biasanya terletak di?',
                'option_a' => 'Luar produk',
                'option_b' => 'Dalam produk',
                'option_c' => 'Di atas produk',
                'option_d' => 'Di samping produk',
                'correct_answer' => 'b',
            ],
            [
                'type' => 'multiple_choice',
                'category' => 'inner',
                'question' => 'Sifat material Inner yang penting adalah?',
                'option_a' => 'Mudah patah',
                'option_b' => 'Tahan panas dan gesekan',
                'option_c' => 'Mudah larut air',
                'option_d' => 'Berwarna warni',
                'correct_answer' => 'b',
            ],
            [
                'type' => 'multiple_choice',
                'category' => 'inner',
                'question' => 'Alat untuk memasang komponen Inner disebut?',
                'option_a' => 'Palu godam',
                'option_b' => 'Jig / Fixture assembly',
                'option_c' => 'Gunting kertas',
                'option_d' => 'Sendok',
                'correct_answer' => 'b',
            ],
            [
                'type' => 'multiple_choice',
                'category' => 'inner',
                'question' => 'Kebersihan Inner part sangat krusial karena?',
                'option_a' => 'Berpengaruh langsung pada fungsi mekanis',
                'option_b' => 'Supaya wangi',
                'option_c' => 'Agar tidak dimakan semut',
                'option_d' => 'Aturan pemerintah',
                'correct_answer' => 'a',
            ],
            [
                'type' => 'multiple_choice',
                'category' => 'inner',
                'question' => 'Defect "Burry" pada Inner dapat menyebabkan?',
                'option_a' => 'Produk lebih kuat',
                'option_b' => 'Kegagalan fungsi / macet',
                'option_c' => 'Produk lebih cepat',
                'option_d' => 'Tidak ada efek',
                'correct_answer' => 'b',
            ],
            // INNER - Essay
            [
                'type' => 'essay',
                'category' => 'inner',
                'question' => 'Jelaskan fungsi spesifik dari part Inner dalam assembly keseluruhan!',
                'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'correct_answer' => null,
            ],
            [
                'type' => 'essay',
                'category' => 'inner',
                'question' => 'Apa saja poin pengecekan kualitas (Quality Point) untuk part Inner?',
                'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'correct_answer' => null,
            ],
            [
                'type' => 'essay',
                'category' => 'inner',
                'question' => 'Mengapa kontaminasi debu sangat berbahaya bagi komponen Inner?',
                'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'correct_answer' => null,
            ],
            [
                'type' => 'essay',
                'category' => 'inner',
                'question' => 'Jelaskan prosedur pembersihan part Inner sebelum perakitan!',
                'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'correct_answer' => null,
            ],
            [
                'type' => 'essay',
                'category' => 'inner',
                'question' => 'Bagaimana cara mengidentifikasi part Inner yang OK dan NG?',
                'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'correct_answer' => null,
            ],

            // ENDPLATE - Multiple Choice
            [
                'type' => 'multiple_choice',
                'category' => 'endplate',
                'question' => 'Endplate berfungsi sebagai?',
                'option_a' => 'Penutup akhir dan penyangga',
                'option_b' => 'Hiasan dinding',
                'option_c' => 'Bahan bakar',
                'option_d' => 'Mainan',
                'correct_answer' => 'a',
            ],
            [
                'type' => 'multiple_choice',
                'category' => 'endplate',
                'question' => 'Proses penyambungan Endplate ke Case biasanya menggunakan?',
                'option_a' => 'Lem kertas',
                'option_b' => 'Baut atau Welding',
                'option_c' => 'Selotip',
                'option_d' => 'Tali rafia',
                'correct_answer' => 'b',
            ],
            [
                'type' => 'multiple_choice',
                'category' => 'endplate',
                'question' => 'Apa yang harus diperhatikan pada permukaan sealing Endplate?',
                'option_a' => 'Kekasaran permukaan (Surface Finish)',
                'option_b' => 'Warna cat',
                'option_c' => 'Merk',
                'option_d' => 'Harga',
                'correct_answer' => 'a',
            ],
            [
                'type' => 'multiple_choice',
                'category' => 'endplate',
                'question' => 'Kebocoran pada Endplate biasanya disebabkan oleh?',
                'option_a' => 'O-ring yang rusak atau permukaan tidak rata',
                'option_b' => 'Udara terlalu dingin',
                'option_c' => 'Warna tidak sesuai',
                'option_d' => 'Terlalu bersih',
                'correct_answer' => 'a',
            ],
            [
                'type' => 'multiple_choice',
                'category' => 'endplate',
                'question' => 'Material Endplate umumnya adalah?',
                'option_a' => 'Kayu',
                'option_b' => 'Logam (Aluminium/Steel)',
                'option_c' => 'Kaca',
                'option_d' => 'Tanah liat',
                'correct_answer' => 'b',
            ],
            // ENDPLATE - Essay
            [
                'type' => 'essay',
                'category' => 'endplate',
                'question' => 'Jelaskan proses pemasangan Endplate yang benar!',
                'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'correct_answer' => null,
            ],
            [
                'type' => 'essay',
                'category' => 'endplate',
                'question' => 'Apa akibatnya jika torsi pengencangan baut Endplate tidak sesuai standar?',
                'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'correct_answer' => null,
            ],
            [
                'type' => 'essay',
                'category' => 'endplate',
                'question' => 'Sebutkan 3 parameter visual check pada Endplate!',
                'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'correct_answer' => null,
            ],
            [
                'type' => 'essay',
                'category' => 'endplate',
                'question' => 'Bagaimana cara menangani Endplate yang mengalami korosi?',
                'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'correct_answer' => null,
            ],
            [
                'type' => 'essay',
                'category' => 'endplate',
                'question' => 'Jelaskan pentingnya flatness (kerataan) pada permukaan Endplate!',
                'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'correct_answer' => null,
            ],
        ];

        foreach ($questions as $q) {
            Evaluation::firstOrCreate(
                ['question' => $q['question'], 'category' => $q['category'] ?? null], // Check uniqueness by question and category
                $q
            );
        }
    }
}
