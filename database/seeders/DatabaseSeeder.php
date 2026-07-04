<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\PelatihanSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@mersiflab.com'],
            ['name' => 'Admin MersifLab', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        User::firstOrCreate(
            ['email' => 'admin2@mersiflab.com'],
            ['name' => 'Admin 2', 'password' => Hash::make('password123'), 'role' => 'admin']
        );

        // Mentor
        User::firstOrCreate(
            ['email' => 'mentor1@example.com'],
            ['name' => 'Mentor Satu', 'password' => Hash::make('password'), 'role' => 'mentor']
        );
        User::firstOrCreate(
            ['email' => 'mentor2@example.com'],
            ['name' => 'Mentor Dua', 'password' => Hash::make('password'), 'role' => 'mentor']
        );

        // Peserta
        User::firstOrCreate(
            ['email' => 'peserta1@example.com'],
            ['name' => 'Peserta Satu', 'password' => Hash::make('password'), 'role' => 'peserta']
        );
        User::firstOrCreate(
            ['email' => 'peserta2@example.com'],
            ['name' => 'Peserta Dua', 'password' => Hash::make('password'), 'role' => 'peserta']
        );

        $this->call(PelatihanSeeder::class);

        // Buat sample pendaftaran
        $pesertaUsers = \App\Models\User::where('role', 'peserta')->get();
        $pelatihans   = \App\Models\Pelatihan::all();
        foreach ($pesertaUsers as $peserta) {
            foreach ($pelatihans->take(2) as $p) {
                \App\Models\Pendaftaran::firstOrCreate(
                    ['user_id' => $peserta->id, 'pelatihan_id' => $p->id],
                    ['status' => 'terverifikasi']
                );
            }
        }
    }
}
