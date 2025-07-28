<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengajuan_hki_id',
        'status',
        'title',
        'description',
        'icon',
        'color',
        'user_id',
        'notes'
    ];

    protected $casts = [
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
     * Relasi ke user yang melakukan aksi
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk mendapatkan tracking berdasarkan pengajuan
     */
    public function scopeForPengajuan($query, $pengajuanId)
    {
        return $query->where('pengajuan_hki_id', $pengajuanId)->orderBy('created_at');
    }

    /**
     * Method untuk membuat tracking status baru
     */
    public static function createTracking($pengajuanId, $status, $title, $description = null, $icon = 'fas fa-circle', $color = 'secondary', $userId = null, $notes = null)
    {
        return self::create([
            'pengajuan_hki_id' => $pengajuanId,
            'status' => $status,
            'title' => $title,
            'description' => $description,
            'icon' => $icon,
            'color' => $color,
            'user_id' => $userId ?? auth()->id(),
            'notes' => $notes
        ]);
    }
}
