<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'pengajuan_hki_id',
        'judul',
        'pesan',
        'status',
        'dibaca'
    ];

    protected $casts = [
        'dibaca' => 'boolean',
        'created_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pengajuanHki()
    {
        return $this->belongsTo(PengajuanHki::class);
    }
}
