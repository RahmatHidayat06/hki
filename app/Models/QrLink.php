<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'hash', 'path', 'filename'
    ];

    public static function findOrCreate(string $path, ?string $filename = null): self
    {
        // Normalise path
        $clean = ltrim($path, '/');
        $existing = self::where('path', $clean)->first();
        if($existing){
            return $existing;
        }
        $hash = substr(md5($clean.time().random_int(1,9999)), 0, 10);
        return self::create([
            'hash' => $hash,
            'path' => $clean,
            'filename' => $filename,
        ]);
    }
} 