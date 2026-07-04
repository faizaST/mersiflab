<?php

namespace Database\Seeders;

use App\Models\Pelatihan;
use Illuminate\Database\Seeder;

class PelatihanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama'          => 'Web Development Bootcamp',
                'deskripsi'     => 'Pelatihan intensif web development mencakup HTML, CSS, JavaScript, dan React.js.',
                'kategori'      => 'Web Development',
                'kuota'         => 40,
                'berbayar'      => true,
                'biaya'         => 500000,
                'lokasi_nama'   => 'Gedung A, Lantai 3',
                'lokasi_alamat' => 'Jl. Sudirman No. 1, Jakarta',
                'lat'           => -6.208800,
                'lng'           => 106.845600,
                'status'        => 'aktif',
            ],
            [
                'nama'          => 'Data Science Fundamental',
                'deskripsi'     => 'Belajar dasar-dasar data science menggunakan Python, Pandas, dan NumPy.',
                'kategori'      => 'Data Science',
                'kuota'         => 30,
                'berbayar'      => false,
                'biaya'         => null,
                'lokasi_nama'   => 'Lab Komputer B',
                'lokasi_alamat' => 'Jl. Gatot Subroto No. 5, Jakarta',
                'lat'           => -6.194400,
                'lng'           => 106.822900,
                'status'        => 'aktif',
            ],
            [
                'nama'          => 'Mobile App Flutter',
                'deskripsi'     => 'Membangun aplikasi mobile cross-platform menggunakan Flutter dan Dart.',
                'kategori'      => 'Mobile Development',
                'kuota'         => 25,
                'berbayar'      => true,
                'biaya'         => 350000,
                'lokasi_nama'   => 'Ruang Seminar C',
                'lokasi_alamat' => 'Jl. Thamrin No. 10, Jakarta',
                'lat'           => -6.195000,
                'lng'           => 106.823000,
                'status'        => 'aktif',
            ],
        ];

        foreach ($data as $item) {
            Pelatihan::firstOrCreate(['nama' => $item['nama']], $item);
        }
    }
}
