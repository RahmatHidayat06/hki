<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasi = Notifikasi::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('notifikasi.index', compact('notifikasi'));
    }

    public function markAsRead($id)
    {
        $notifikasi = Notifikasi::findOrFail($id);
        
        if ($notifikasi->user_id === Auth::id()) {
            $notifikasi->update([
                'dibaca' => true,
                'status' => 'read'
            ]);
            return Redirect::back()->with('success', 'Notifikasi berhasil ditandai sebagai sudah dibaca');
        }

        return Redirect::back()->with('error', 'Anda tidak memiliki akses untuk notifikasi ini');
    }

    public function markAllAsRead()
    {
        $count = Notifikasi::where('user_id', Auth::id())
            ->where('dibaca', false)
            ->count();

        if ($count > 0) {
            Notifikasi::where('user_id', Auth::id())
                ->where('dibaca', false)
                ->update([
                    'dibaca' => true,
                    'status' => 'read'
                ]);
            return Redirect::back()->with('success', 'Semua notifikasi berhasil ditandai sebagai sudah dibaca');
        }

        return Redirect::back()->with('info', 'Tidak ada notifikasi yang perlu ditandai sebagai sudah dibaca');
    }

    public function destroy($id)
    {
        $notifikasi = Notifikasi::findOrFail($id);
        
        if ($notifikasi->user_id === Auth::id()) {
            $notifikasi->delete();
            return Redirect::back()->with('success', 'Notifikasi berhasil dihapus');
        }

        return Redirect::back()->with('error', 'Anda tidak memiliki akses untuk menghapus notifikasi ini');
    }
}