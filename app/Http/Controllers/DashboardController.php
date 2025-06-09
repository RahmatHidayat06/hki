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
        if ($user->role === 'admin_p3m') {
            return Redirect::to(route('validasi.index'));
        }
        // Direktur dan role lain langsung ke dashboard.index
            $all = PengajuanHki::where('user_id', $user->id)->get();
            $totalPengajuan = $all->count();
            $tidakLengkap = $all->filter(function($item) {
            return $item->status !== 'disetujui' && (empty($item->judul_karya) || empty($item->created_at));
            });
            $belumDisetujui = $all->filter(function($item) {
                return $item->status !== 'disetujui';
            });
            return view('dashboard.index', compact('totalPengajuan', 'tidakLengkap', 'belumDisetujui'));
    }
}