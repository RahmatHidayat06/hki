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
        'no_telp',
        'id_sinta',
        'jumlah_pencipta',
        'identitas_ciptaan',
        'sub_jenis_ciptaan',
        'tanggal_pertama_kali_diumumkan',
        'kota_pertama_kali_diumumkan',
        'billing_code',
        'bukti_pembayaran',
        'sertifikat',
        'role',
        'catatan_admin',
        'tanggal_validasi',
        'tanggal_surat',
        'alamat_pencipta',
        'signature_pencipta',
        'gunakan_materai',
        'nomor_submisi_djki',
        'tanggal_submisi_djki',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'datetime',
        'tanggal_selesai' => 'date',
        'tanggal_validasi' => 'datetime',
        'tanggal_surat' => 'date',
        'alamat_pencipta' => 'array',
        'signature_pencipta' => 'array',
        'gunakan_materai' => 'boolean',
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

    /**
     * Relasi ke tracking statuses
     */
    public function trackingStatuses()
    {
        return $this->hasMany(TrackingStatus::class)->orderBy('created_at');
    }

    /**
     * Relasi ke signatures
     */
    public function signatures()
    {
        return $this->hasMany(Signature::class)->orderBy('pencipta_ke');
    }

    /**
     * Get the latest tracking status
     */
    public function latestTracking()
    {
        return $this->hasOne(TrackingStatus::class)->latest();
    }

    /**
     * Check apakah semua signature sudah ditandatangani
     */
    public function allSignaturesSigned()
    {
        return Signature::allSignedForPengajuan($this->id);
    }

    /**
     * Get signature progress percentage
     */
    public function getSignatureProgress()
    {
        return Signature::getProgressForPengajuan($this->id);
    }

    /**
     * Create tracking status ketika status berubah
     */
    public function addTracking($status, $title, $description = null, $icon = 'fas fa-circle', $color = 'secondary', $notes = null)
    {
        return TrackingStatus::createTracking($this->id, $status, $title, $description, $icon, $color, auth()->id() ?? null, $notes);
    }
}