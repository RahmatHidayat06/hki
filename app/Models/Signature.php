<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Signature extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengajuan_hki_id',
        'pencipta_ke',
        'nama_pencipta',
        'email_pencipta',
        'nama_ttd',
        'posisi',
        'signature_path',
        'ktp_path',
        'signature_image_path',
        'signed_at',
        'signed_by',
        'status',
        'rejection_reason',
        'signature_token',
        'page',
        'x_percent',
        'y_percent',
        'width_percent',
        'height_percent'
    ];

    protected $casts = [
        'signed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke pengajuan HKI
     */
    public function pengajuanHki()
    {
        return $this->belongsTo(PengajuanHki::class);
    }

    /**
     * Relasi ke user yang menandatangani
     */
    public function signedBy()
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    /**
     * Scope untuk mendapatkan signature yang belum ditandatangani
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope untuk mendapatkan signature yang sudah ditandatangani
     */
    public function scopeSigned($query)
    {
        return $query->where('status', 'signed');
    }

    /**
     * Scope untuk mendapatkan signature berdasarkan pengajuan
     */
    public function scopeForPengajuan($query, $pengajuanId)
    {
        return $query->where('pengajuan_hki_id', $pengajuanId)->orderBy('pencipta_ke');
    }

    /**
     * Generate token unik untuk tanda tangan
     */
    public static function generateToken()
    {
        do {
            $token = Str::random(64);
        } while (self::where('signature_token', $token)->exists());
        
        return $token;
    }

    /**
     * Method untuk membuat signature records berdasarkan data pencipta
     */
    public static function createSignaturesForPengajuan($pengajuanId, $penciptaData)
    {
        $signatures = [];
        
        foreach ($penciptaData as $index => $pencipta) {
            $signatures[] = self::create([
                'pengajuan_hki_id' => $pengajuanId,
                'pencipta_ke' => $index + 1,
                'nama_pencipta' => $pencipta['nama'],
                'email_pencipta' => $pencipta['email'] ?? null,
                'nama_ttd' => $pencipta['nama_ttd'] ?? $pencipta['nama'],
                'posisi' => $pencipta['posisi'] ?? 'kiri',
                'signature_token' => self::generateToken(),
                'status' => 'pending'
            ]);
        }
        
        return $signatures;
    }

    /**
     * Check apakah semua signature sudah ditandatangani
     */
    public static function allSignedForPengajuan($pengajuanId)
    {
        $totalSignatures = self::where('pengajuan_hki_id', $pengajuanId)->count();
        $signedSignatures = self::where('pengajuan_hki_id', $pengajuanId)
                                ->where('status', 'signed')
                                ->count();
        
        return $totalSignatures > 0 && $totalSignatures === $signedSignatures;
    }

    /**
     * Get progress percentage untuk pengajuan
     */
    public static function getProgressForPengajuan($pengajuanId)
    {
        $totalSignatures = self::where('pengajuan_hki_id', $pengajuanId)->count();
        $signedSignatures = self::where('pengajuan_hki_id', $pengajuanId)
                                ->where('status', 'signed')
                                ->count();
        
        return $totalSignatures > 0 ? round(($signedSignatures / $totalSignatures) * 100) : 0;
    }
}
 