<?php

namespace App\Http\Controllers;

use App\Models\PengajuanHki;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

class ValidationController extends Controller
{
    protected $redirector;

    public function __construct(Redirector $redirector)
    {
        $this->middleware('auth');
        $this->middleware('role:admin_p3m');
        $this->redirector = $redirector;
    }

    public function index()
    {
        $pengajuan = PengajuanHki::whereIn('status', ['pending', 'validated'])
            ->latest()
            ->paginate(10);
            
        return view('validasi.index', compact('pengajuan'));
    }

    public function show(PengajuanHki $pengajuan)
    {
        return view('validasi.show', compact('pengajuan'));
    }

    public function approve(PengajuanHki $pengajuan): RedirectResponse
    {
        $pengajuan->update([
            'status' => 'validated',
            'validated_at' => now(),
            'validated_by' => Auth::id()
        ]);

        return $this->redirector->route('validasi.index')
            ->with('success', 'Pengajuan berhasil divalidasi.');
    }

    public function reject(Request $request, PengajuanHki $pengajuan): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        $pengajuan->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'validated_at' => now(),
            'validated_by' => Auth::id()
        ]);

        return $this->redirector->route('validasi.index')
            ->with('success', 'Pengajuan berhasil ditolak.');
    }
} 