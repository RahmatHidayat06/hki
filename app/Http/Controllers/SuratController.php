<?php

namespace App\Http\Controllers;

use App\Models\PengajuanHki;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class SuratController extends Controller
{
    // Generate surat pengalihan
    public function pengalihan($id)
    {
        $pengajuan = PengajuanHki::findOrFail($id);
        $direktur = $pengajuan->user; // atau ambil user dengan role direktur
        $ttdPath = $direktur && $direktur->ttd_path ? storage_path('app/public/' . $direktur->ttd_path) : null;
        $materaiPath = public_path('img/materai10rb.png');

        // Ensure the document has the director's signature
        if (!$ttdPath) {
            return back()->with('error', 'Tanda tangan direktur tidak ditemukan.');
        }

        $pdf = PDF::loadView('surat.pengalihan', compact('pengajuan', 'ttdPath', 'materaiPath'));
        return $pdf->download('Surat_Pengalihan.pdf');
    }

    // Generate surat pernyataan
    public function pernyataan($id)
    {
        $pengajuan = PengajuanHki::findOrFail($id);
        $direktur = $pengajuan->user; // atau ambil user dengan role direktur
        $ttdPath = $direktur && $direktur->ttd_path ? storage_path('app/public/' . $direktur->ttd_path) : null;
        $materaiPath = public_path('img/materai10rb.png');

        // Ensure the document has the director's signature
        if (!$ttdPath) {
            return back()->with('error', 'Tanda tangan direktur tidak ditemukan.');
        }

        // Ensure the document includes a stamp
        if (!$materaiPath) {
            return back()->with('error', 'Materai tidak ditemukan.');
        }

        $pdf = PDF::loadView('surat.pernyataan', compact('pengajuan', 'ttdPath', 'materaiPath'));
        return $pdf->download('Surat_Pernyataan.pdf');
    }
} 