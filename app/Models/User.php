<?php

namespace App\Models;

/**
 * @method bool update(array $attributes = [], array $options = [])
 */
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'nama_lengkap',
        'email',
        'no_hp',
        'password',
        'role',
        'ttd_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function pengajuanHkis()
    {
        return $this->hasMany(PengajuanHki::class);
    }

    public function notifikasis()
    {
        return $this->hasMany(Notifikasi::class);
    }

    /**
     * Accessor for backward compatibility with 'name' attribute
     */
    public function getNameAttribute()
    {
        return $this->nama_lengkap;
    }

    /**
     * Mutator for backward compatibility with 'name' attribute
     */
    public function setNameAttribute($value)
    {
        $this->attributes['nama_lengkap'] = $value;
    }
}