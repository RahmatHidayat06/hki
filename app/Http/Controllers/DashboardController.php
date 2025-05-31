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
        } elseif ($user->role === 'direktur') {
            return Redirect::to(route('persetujuan.index'));
        } else {
            $pengajuan = PengajuanHki::where('user_id', $user->id)->paginate(10);
            return view('dashboard.index', compact('pengajuan'));
        }
    }
}