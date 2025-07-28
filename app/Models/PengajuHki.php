<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuHki extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'email',
        'no_telp',
        'alamat',
        'pengajuan_hki_id',
        'kewarganegaraan',
        'kodepos',
    ];
}
