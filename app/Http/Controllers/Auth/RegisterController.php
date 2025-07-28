<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /**
     * Show the application registration form.
     */
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(Request $request): RedirectResponse
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        // Login user setelah berhasil register
        auth()->login($user);

        return Redirect::to(route('dashboard'))
            ->with('success', 'Registrasi berhasil! Selamat datang di Sistem Pengajuan HKI.');
    }

    /**
     * Get a validator for an incoming registration request.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => ['required', 'string', 'max:50', 'unique:users', 'regex:/^[a-zA-Z0-9_\.@]+$/'],
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users'],
            'no_ktp' => ['required', 'string', 'min:16', 'max:16', 'regex:/^[0-9]{16}$/'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:L,P'],
            'nationality' => ['required', 'string', 'max:50'],
            'nip_nidn' => ['nullable', 'string', 'max:20', 'required_if:role,dosen'],
            'id_sinta' => ['nullable', 'string', 'max:20', 'required_if:role,dosen'],
            'no_telp' => ['required', 'string', 'min:10', 'max:10', 'regex:/^[0-9]{10}$/'],
            'role' => ['required', 'in:dosen,mahasiswa'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'username.regex' => 'Username hanya boleh mengandung huruf, angka, underscore, titik, dan @.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'no_ktp.required' => 'No. KTP wajib diisi.',
            'no_ktp.min' => 'No. KTP harus 16 digit.',
            'no_ktp.max' => 'No. KTP harus 16 digit.',
            'no_ktp.regex' => 'No. KTP harus berupa 16 digit angka.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'gender.in' => 'Jenis kelamin harus Laki-laki atau Perempuan.',
            'nationality.required' => 'Kewarganegaraan wajib dipilih.',
            'nip_nidn.required_if' => 'NIP/NIDN wajib diisi untuk Dosen.',
            'nip_nidn.max' => 'NIP/NIDN maksimal 20 karakter.',
            'id_sinta.required_if' => 'ID Sinta wajib diisi untuk Dosen.',
            'id_sinta.max' => 'ID Sinta maksimal 20 karakter.',
            'no_telp.required' => 'Nomor HP wajib diisi.',
            'no_telp.min' => 'Nomor HP harus 10 digit setelah 08.',
            'no_telp.max' => 'Nomor HP harus 10 digit setelah 08.',
            'no_telp.regex' => 'Nomor HP harus berupa 10 digit angka.',
            'role.required' => 'Tipe pendaftar wajib dipilih.',
            'role.in' => 'Tipe pendaftar harus Dosen atau Mahasiswa.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(array $data)
    {
        // Format phone number with 08 prefix
        $formattedPhoneNumber = '08' . $data['no_telp'];
        
        return User::create([
            'username' => $data['username'],
            'nama_lengkap' => $data['nama_lengkap'],
            'email' => $data['email'],
            'no_ktp' => $data['no_ktp'],
            'tanggal_lahir' => $data['tanggal_lahir'],
            'gender' => $data['gender'],
            'nationality' => $data['nationality'],
            'nip_nidn' => $data['nip_nidn'] ?? null,
            'id_sinta' => $data['id_sinta'] ?? null,
            'no_telp' => $formattedPhoneNumber,
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
        ]);
    }
} 