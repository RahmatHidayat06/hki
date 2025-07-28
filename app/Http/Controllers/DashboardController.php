<?php

namespace App\Http\Controllers;

use App\Models\PengajuanHki;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View|RedirectResponse
    {
        $user = Auth::user();
        
        // Redirect berdasarkan role
        if ($user->role === 'admin') {
            return Redirect::to(route('admin.dashboard'));
        }

        if ($user->role === 'direktur') {
            // Dashboard untuk direktur - exclude draft from all statistics and data
            $menunggu = PengajuanHki::where('status', 'menunggu_validasi_direktur')->count();
            $disetujui = PengajuanHki::where('status', 'divalidasi_sedang_diproses')->count();
            $ditolak = PengajuanHki::where('status', 'ditolak')->count();
            
            // Pengajuan baru menunggu persetujuan (exclude draft)
            $pengajuanBaru = PengajuanHki::where('status', 'menunggu_validasi_direktur')
                ->latest()
                ->take(5)
                ->get();
            
            return view('dashboard', compact('menunggu', 'disetujui', 'ditolak', 'pengajuanBaru'));
        }
        
        // Untuk dosen dan mahasiswa - exclude draft from own statistics
        $all = PengajuanHki::where('user_id', $user->id)
            ->where('status', '!=', 'draft')
            ->get();
            $totalPengajuan = $all->count();
            $tidakLengkap = $all->filter(function($item) {
            return $item->status !== 'disetujui' && (empty($item->judul_karya) || empty($item->created_at));
            });
            $belumDisetujui = $all->filter(function($item) {
                return $item->status !== 'disetujui';
            });
            $diproses = $all->where('status', 'divalidasi_sedang_diproses')->count();
            return view('dashboard.index', compact('totalPengajuan', 'tidakLengkap', 'belumDisetujui', 'diproses'));
    }
}