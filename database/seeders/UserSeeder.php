<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin P3M
        User::create([
            'name' => 'Admin P3M',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'nama_lengkap' => 'Admin P3M',
            'email' => 'admin@example.com',
            'no_telp' => '08123456789'
        ]);

        // Direktur
        User::create([
            'name' => 'Direktur',
            'username' => 'direktur',
            'password' => Hash::make('direktur123'),
            'role' => 'direktur',
            'nama_lengkap' => 'Direktur',
            'email' => 'direktur@example.com',
            'no_telp' => '08123456790'
        ]);

        // Dosen
        User::create([
            'name' => 'Dosen',
            'username' => 'dosen',
            'password' => Hash::make('dosen123'),
            'role' => 'dosen',
            'nama_lengkap' => 'Dosen',
            'email' => 'dosen@example.com',
            'no_telp' => '08123456791'
        ]);

        // Mahasiswa
        User::create([
            'name' => 'Mahasiswa',
            'username' => 'mahasiswa',
            'password' => Hash::make('mahasiswa123'),
            'role' => 'mahasiswa',
            'nama_lengkap' => 'Mahasiswa',
            'email' => 'mahasiswa@example.com',
            'no_telp' => '08123456792'
        ]);
    }
}