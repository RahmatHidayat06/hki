<?php

namespace App\Policies;

use App\Models\PengajuanHki;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PengajuanHkiPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['dosen', 'admin', 'direktur']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PengajuanHki $pengajuanHki): bool
    {
        return $user->id === $pengajuanHki->user_id || 
               in_array($user->role, ['admin', 'direktur']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'dosen';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PengajuanHki $pengajuanHki): bool
    {
        return $user->id === $pengajuanHki->user_id && 
               $pengajuanHki->status === 'pending' && 
               $user->role === 'dosen';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PengajuanHki $pengajuanHki): bool
    {
        return $user->id === $pengajuanHki->user_id && 
               $pengajuanHki->status === 'pending' && 
               $user->role === 'dosen';
    }
} 