<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\PengajuanHki;

class PengajuanHkiSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil atau buat 5 dosen
        $dosenUsers = User::where('role', 'dosen')->limit(5)->get();
        if ($dosenUsers->count() < 5) {
            $remaining = 5 - $dosenUsers->count();
            for ($i = 1; $i <= $remaining; $i++) {
                $dosenUsers->push(User::create([
                    'name' => 'Dosen Dummy ' . $i,
                    'username' => 'dosen_dummy_' . $i,
                    'password' => Hash::make('password'),
                    'role' => 'dosen',
                    'email' => 'dosen_dummy_' . $i . '@example.com',
                ]));
            }
        }

        // Ambil atau buat 5 mahasiswa
        $mahasiswaUsers = User::where('role', 'mahasiswa')->limit(5)->get();
        if ($mahasiswaUsers->count() < 5) {
            $remaining = 5 - $mahasiswaUsers->count();
            for ($i = 1; $i <= $remaining; $i++) {
                $mahasiswaUsers->push(User::create([
                    'name' => 'Mahasiswa Dummy ' . $i,
                    'username' => 'mhs_dummy_' . $i,
                    'password' => Hash::make('password'),
                    'role' => 'mahasiswa',
                    'email' => 'mhs_dummy_' . $i . '@example.com',
                ]));
            }
        }

        $faker = \Faker\Factory::create('id_ID');

        $createPengajuan = function (User $user, $index) use ($faker) {
            return PengajuanHki::create([
                'user_id' => $user->id,
                'judul_karya' => 'Judul Karya ' . $user->role . ' #' . $index,
                'deskripsi' => $faker->paragraph(2),
                'status' => 'menunggu_validasi',
                'nomor_pengajuan' => Str::uuid()->toString(),
                'tanggal_pengajuan' => now(),
                'role' => $user->role,
            ]);
        };

        foreach ($dosenUsers as $idx => $user) {
            $createPengajuan($user, $idx + 1);
        }
        foreach ($mahasiswaUsers as $idx => $user) {
            $createPengajuan($user, $idx + 1);
        }
    }
} 