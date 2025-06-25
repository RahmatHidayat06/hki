<?php

namespace App\Http\Controllers;

use App\Models\PengajuanHki;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class SuratController extends Controller
{
    // Generate surat pengalihan
    public function pengalihan(PengajuanHki $pengajuan)
    {
        // Tanda tangan diambil dari user yang terkait dengan pengajuan (si pengusul)
        $pengusul = $pengajuan->user; 
        $ttdPath = ($pengusul && $pengusul->ttd_path) ? storage_path('app/public/' . $pengusul->ttd_path) : null;
        
        // Path ke file gambar materai
        $materaiPath = public_path('img/materai10rb.png');

        $data = [
            'pengajuan' => $pengajuan,
            'ttdPath' => $ttdPath,
            'materaiPath' => $materaiPath,
        ];

        $pdf = Pdf::loadView('surat.pengalihan', $data);
        return $pdf->download('Surat_Pengalihan_Hak_Cipta_'.Str::slug($pengajuan->judul_karya).'.pdf');
    }

    // Generate surat pernyataan
    public function pernyataan(PengajuanHki $pengajuan)
    {
        // Tanda tangan diambil dari user yang terkait dengan pengajuan (si pengusul)
        $pengusul = $pengajuan->user;
        $ttdPath = ($pengusul && $pengusul->ttd_path) ? storage_path('app/public/' . $pengusul->ttd_path) : null;

        // Path ke file gambar materai
        $materaiPath = public_path('img/materai10rb.png');
        
        $data = [
            'pengajuan' => $pengajuan,
            'ttdPath' => $ttdPath,
            'materaiPath' => $materaiPath,
        ];

        $pdf = Pdf::loadView('surat.pernyataan', $data);
        return $pdf->download('Surat_Pernyataan_'.Str::slug($pengajuan->judul_karya).'.pdf');
    }
} 