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
        // Clear existing evaluations to avoid duplicates if run multiple times (optional, but good for seeding)
        // Evaluation::truncate(); // Be careful with truncate if you want to keep data. better use createOrFirst if needed, but for seeding fresh is usually ok. 
        // For now I will just append or maybe check existence if I want to be safe, but typically seeders run on fresh db.
        // Let's assume we want a fresh set or robust set. I'll just create them. 
        // To prevent duplicates if the user runs db:seed multiple times, I'll use firstOrCreate based on the question text.

        $questions = [
            // K3 & Keselamatan Kerja
            [
                'type' => 'multiple_choice',
                'question' => 'Apa langkah pertama yang harus dilakukan jika terjadi kebakaran kecil di area kerja?',
                'option_a' => 'Lari keluar gedung dengan panik',
                'option_b' => 'Menggunakan APAR untuk memadamkan api',
                'option_c' => 'Melapor ke atasan tanpa melakukan apa-apa',
                'option_d' => 'Menyiram dengan air minum',
                'correct_answer' => 'b',
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Alat Pelindung Diri (APD) apa yang wajib digunakan saat memasuki area produksi?',
                'option_a' => 'Kacamata hitam dan topi',
                'option_b' => 'Sepatu safety dan helm safety',
                'option_c' => 'Sandal jepit dan masker',
                'option_d' => 'Jaket kulit dan sarung tangan wol',
                'correct_answer' => 'b',
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Apa arti dari rambu K3 berwarna kuning dengan simbol segitiga hitam?',
                'option_a' => 'Larangan',
                'option_b' => 'Wajib ditaati',
                'option_c' => 'Informasi umum',
                'option_d' => 'Peringatan bahaya (Warning)',
                'correct_answer' => 'd',
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Mengapa area kerja harus selalu dijaga kebersihannya (5R)?',
                'option_a' => 'Agar terlihat bagus di foto',
                'option_b' => 'Untuk mengurangi risiko kecelakaan dan meningkatkan efisiensi',
                'option_c' => 'Karena diperintah atasan saja',
                'option_d' => 'Supaya cleaning service tidak bekerja',
                'correct_answer' => 'b',
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Apa yang harus dilakukan jika melihat tumpahan oli di lantai produksi?',
                'option_a' => 'Membiarkannya sampai kering',
                'option_b' => 'Melompati tumpahan tersebut',
                'option_c' => 'Segera membersihkan atau melapor ke petugas kebersihan dan memasang tanda bahaya',
                'option_d' => 'Menutupinya dengan kardus',
                'correct_answer' => 'c',
            ],
            
            // Etika & Aturan Kerja
            [
                'type' => 'multiple_choice',
                'question' => 'Berapa jam kerja standar dalam satu shift normal?',
                'option_a' => '6 Jam',
                'option_b' => '7 Jam',
                'option_c' => '8 Jam',
                'option_d' => '9 Jam',
                'correct_answer' => 'c',
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Kapan waktu yang tepat untuk melakukan absen masuk?',
                'option_a' => 'Setelah mulai bekerja',
                'option_b' => 'Saat istirahat siang',
                'option_c' => 'Sebelum jam kerja dimulai',
                'option_d' => 'Kapan saja ingat',
                'correct_answer' => 'c',
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Apa tindakan yang benar jika Anda sakit dan tidak bisa masuk kerja?',
                'option_a' => 'Tidak perlu memberi kabar',
                'option_b' => 'Memberi kabar ke teman kerja saja',
                'option_c' => 'Menghubungi atasan/HRD dan menyertakan surat keterangan dokter',
                'option_d' => 'Datang siang hari',
                'correct_answer' => 'c',
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Bagaimana sikap yang baik saat menerima kritik dari atasan?',
                'option_a' => 'Marah dan membantah',
                'option_b' => 'Mendengarkan, menerima masukan, dan memperbaiki kinerja',
                'option_c' => 'Diam saja tapi tidak peduli',
                'option_d' => 'Menyalahkan rekan kerja lain',
                'correct_answer' => 'b',
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Apa yang dimaksud dengan integritas dalam bekerja?',
                'option_a' => 'Bekerja hanya saat diawasi',
                'option_b' => 'Jujur, bertanggung jawab, dan konsisten dalam tindakan',
                'option_c' => 'Sering mengambil barang kantor',
                'option_d' => 'Menceritakan rahasia perusahaan',
                'correct_answer' => 'b',
            ],

            // Soal Essay
            [
                'type' => 'essay',
                'question' => 'Jelaskan prosedur evakuasi darurat yang Anda ketahui jika alarm kebakaran berbunyi!',
                'option_a' => null,
                'option_b' => null,
                'option_c' => null,
                'option_d' => null,
                'correct_answer' => null,
            ],
            [
                'type' => 'essay',
                'question' => 'Sebutkan dan jelaskan 5R (Ringkas, Rapi, Resik, Rawat, Rajin) dalam lingkungan kerja!',
                'option_a' => null,
                'option_b' => null,
                'option_c' => null,
                'option_d' => null,
                'correct_answer' => null,
            ],
            [
                'type' => 'essay',
                'question' => 'Apa yang Anda lakukan jika menemukan rekan kerja melakukan pelanggaran keselamatan kerja yang berbahaya?',
                'option_a' => null,
                'option_b' => null,
                'option_c' => null,
                'option_d' => null,
                'correct_answer' => null,
            ],
            [
                'type' => 'essay',
                'question' => 'Jelaskan pentingnya teamwork (kerja sama tim) dalam mencapai target produksi!',
                'option_a' => null,
                'option_b' => null,
                'option_c' => null,
                'option_d' => null,
                'correct_answer' => null,
            ],
            [
                'type' => 'essay',
                'question' => 'Bagaimana cara Anda menangani komplain atau masalah teknis yang terjadi pada mesin produksi saat shift berjalan?',
                'option_a' => null,
                'option_b' => null,
                'option_c' => null,
                'option_d' => null,
                'correct_answer' => null,
            ],
        ];

        foreach ($questions as $q) {
            Evaluation::firstOrCreate(
                ['question' => $q['question']],
                [
                    'type' => $q['type'],
                    'option_a' => $q['option_a'],
                    'option_b' => $q['option_b'],
                    'option_c' => $q['option_c'],
                    'option_d' => $q['option_d'],
                    'correct_answer' => $q['correct_answer'],
                ]
            );
        }
    }
}
