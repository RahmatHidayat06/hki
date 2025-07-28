<?php

namespace App\Models;

/**
 * @method bool update(array $attributes = [], array $options = [])
 */
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'nama_lengkap',
        'email',
        'no_ktp',
        'tanggal_lahir',
        'gender',
        'nationality',
        'nip_nidn',
        'id_sinta',
        'no_telp',
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
        'tanggal_lahir' => 'date',
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

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is direktur
     */
    public function isDirektur()
    {
        return $this->role === 'direktur';
    }

    /**
     * Check if user is dosen
     */
    public function isDosen()
    {
        return $this->role === 'dosen';
    }

    /**
     * Check if user is mahasiswa
     */
    public function isMahasiswa()
    {
        return $this->role === 'mahasiswa';
    }
}