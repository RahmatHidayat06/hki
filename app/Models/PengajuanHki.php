<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanHki extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_hkis';

    protected $fillable = [
        'user_id',
        'judul_karya',
        'deskripsi',
        'file_karya',
        'file_dokumen_pendukung',
        'status',
        'catatan_validasi',
        'catatan_persetujuan',
        'nomor_pengajuan',
        'tanggal_pengajuan',
        'nama_pengusul',
        'nip_nidn',
        'no_hp',
        'id_sinta',
        'jumlah_pencipta',
        'identitas_ciptaan',
        'sub_jenis_ciptaan',
        'tanggal_pertama_kali_diumumkan',
        'tahun_usulan',
        'billing_code',
        'bukti_pembayaran',
        'sertifikat',
        'role',
        'catatan_admin',
        'tanggal_validasi',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'datetime',
        'tanggal_selesai' => 'date',
        'tanggal_validasi' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the applicant (user) for the HKI submission.
     */
    public function pemohon()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pengaju()
    {
        return $this->hasMany(\App\Models\PengajuHki::class, 'pengajuan_hki_id');
    }

    public function dokumen()
    {
        return $this->hasMany(DokumenHki::class);
    }

    public function persetujuanDirektur()
    {
        return $this->hasOne(\App\Models\PersetujuanDirektur::class, 'pengajuan_hki_id');
    }
    public function validasiP3m()
    {
        return $this->hasOne(\App\Models\ValidasiP3m::class, 'pengajuan_hki_id');
    }

    public function riwayatStatus()
    {
        return $this->hasMany(RiwayatStatus::class);
    }
}