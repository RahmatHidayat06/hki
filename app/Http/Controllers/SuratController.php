<?php

namespace App\Http\Controllers;

use App\Models\PengajuanHki;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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

        // Format tanggal surat
        $tanggalSurat = $pengajuan->tanggal_surat ? 
            \Carbon\Carbon::parse($pengajuan->tanggal_surat)->translatedFormat('d F Y') : 
            \Carbon\Carbon::now()->translatedFormat('d F Y');

        $data = [
            'pengajuan' => $pengajuan,
            'ttdPath' => $ttdPath,
            'materaiPath' => $materaiPath,
            'tanggalSurat' => $tanggalSurat,
        ];

        $pdf = Pdf::loadView('surat.pengalihan', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions(['defaultPaperSize' => 'a4', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'defaultFont' => 'times']);
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
        
        // Format tanggal surat
        $tanggalSurat = $pengajuan->tanggal_surat ? 
            \Carbon\Carbon::parse($pengajuan->tanggal_surat)->translatedFormat('d F Y') : 
            \Carbon\Carbon::now()->translatedFormat('d F Y');
        
        $data = [
            'pengajuan' => $pengajuan,
            'ttdPath' => $ttdPath,
            'materaiPath' => $materaiPath,
            'tanggalSurat' => $tanggalSurat,
        ];

        $pdf = Pdf::loadView('surat.pernyataan', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions(['defaultPaperSize' => 'a4', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        return $pdf->download('Surat_Pernyataan_'.Str::slug($pengajuan->judul_karya).'.pdf');
    }

    // Generate Form Permohonan Pendaftaran Ciptaan
    public function formPermohonanPendaftaran(PengajuanHki $pengajuan)
    {
        // Tanda tangan diambil dari user yang terkait dengan pengajuan (si pengusul)
        $pengusul = $pengajuan->user; 
        $ttdPath = ($pengusul && $pengusul->ttd_path) ? storage_path('app/public/' . $pengusul->ttd_path) : null;
        
        $data = [
            'pengajuan' => $pengajuan,
            'ttdPath' => $ttdPath,
        ];

        $pdf = Pdf::loadView('surat.form_permohonan_pendaftaran', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions(['defaultPaperSize' => 'a4', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'defaultFont' => 'times']);
        return $pdf->download('Form_Permohonan_Pendaftaran_Ciptaan_'.Str::slug($pengajuan->judul_karya).'.pdf');
    }

    /**
     * Auto-generate surat pengalihan and save to storage
     */
    public function autoGeneratePengalihan(PengajuanHki $pengajuan)
    {
        try {
            $pengusul = $pengajuan->user;
            $ttdPath = ($pengusul && $pengusul->ttd_path) ? storage_path('app/public/' . $pengusul->ttd_path) : null;
            $materaiPath = public_path('img/materai10rb.png');

            // Format tanggal surat
            $tanggalSurat = $pengajuan->tanggal_surat ? 
                \Carbon\Carbon::parse($pengajuan->tanggal_surat)->translatedFormat('d F Y') : 
                \Carbon\Carbon::now()->translatedFormat('d F Y');

            $data = [
                'pengajuan' => $pengajuan,
                'ttdPath' => $ttdPath,
                'materaiPath' => $materaiPath,
                'tanggalSurat' => $tanggalSurat,
            ];

            $pdf = Pdf::loadView('surat.pengalihan', $data)
                ->setPaper('a4', 'portrait')
                ->setOptions(['defaultPaperSize' => 'a4', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
            
            // Save to storage
            $fileName = 'surat_pengalihan_' . $pengajuan->id . '_' . time() . '.pdf';
            $filePath = 'dokumen_pengalihan/' . $fileName;
            
            Storage::disk('public')->put($filePath, $pdf->output());
            
            Log::info('Auto-generated Surat Pengalihan', [
                'pengajuan_id' => $pengajuan->id,
                'file_path' => $filePath
            ]);

            return $filePath;
        } catch (\Exception $e) {
            Log::error('Error auto-generating Surat Pengalihan', [
                'pengajuan_id' => $pengajuan->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Auto-generate surat pernyataan and save to storage
     */
    public function autoGeneratePernyataan(PengajuanHki $pengajuan)
    {
        try {
            $pengusul = $pengajuan->user;
            $ttdPath = ($pengusul && $pengusul->ttd_path) ? storage_path('app/public/' . $pengusul->ttd_path) : null;
            $materaiPath = public_path('img/materai10rb.png');

            // Format tanggal surat
            $tanggalSurat = $pengajuan->tanggal_surat ? 
                \Carbon\Carbon::parse($pengajuan->tanggal_surat)->translatedFormat('d F Y') : 
                \Carbon\Carbon::now()->translatedFormat('d F Y');

            $data = [
                'pengajuan' => $pengajuan,
                'ttdPath' => $ttdPath,
                'materaiPath' => $materaiPath,
                'tanggalSurat' => $tanggalSurat,
            ];

            $pdf = Pdf::loadView('surat.pernyataan', $data)
                ->setPaper('a4', 'portrait')
                ->setOptions(['defaultPaperSize' => 'a4', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
            
            // Save to storage
            $fileName = 'surat_pernyataan_' . $pengajuan->id . '_' . time() . '.pdf';
            $filePath = 'dokumen_pernyataan/' . $fileName;
            
            Storage::disk('public')->put($filePath, $pdf->output());
            
            Log::info('Auto-generated Surat Pernyataan', [
                'pengajuan_id' => $pengajuan->id,
                'file_path' => $filePath
            ]);

            return $filePath;
        } catch (\Exception $e) {
            Log::error('Error auto-generating Surat Pernyataan', [
                'pengajuan_id' => $pengajuan->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Generate Form Permohonan Pendaftaran Ciptaan
     */
    public function formPermohonan(PengajuanHki $pengajuan)
    {
        $pengusul = $pengajuan->user;
        $ttdPath = ($pengusul && $pengusul->ttd_path) ? storage_path('app/public/' . $pengusul->ttd_path) : null;
        $data = [
            'pengajuan' => $pengajuan,
            'ttdPath' => $ttdPath,
        ];
        $pdf = Pdf::loadView('surat.form_permohonan_pendaftaran', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions(['defaultPaperSize' => 'a4', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'defaultFont' => 'times']);
        
        return $pdf->download('Form_Permohonan_Pendaftaran_Ciptaan_'.Str::slug($pengajuan->judul_karya).'.pdf');
    }

    /**
     * Auto-generate Form Permohonan Pendaftaran (untuk internal use)
     */
    public function autoGenerateFormPermohonan(PengajuanHki $pengajuan)
    {
        try {
            // Sertakan ttdPath jika tersedia (diambil dari user pemohon)
            $pengusul = $pengajuan->user;
            $ttdPath = ($pengusul && $pengusul->ttd_path)
                ? storage_path('app/public/' . ltrim($pengusul->ttd_path, '/'))
                : null;

            $data = [
                'pengajuan' => $pengajuan,
                'ttdPath' => $ttdPath,
            ];

            // Gunakan view yang benar
            $pdf = \PDF::loadView('surat.form_permohonan_pendaftaran', $data)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultPaperSize' => 'a4',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'defaultFont' => 'times'
                ]);
            
            // Save to storage (disk public)
            $fileName = 'form_permohonan_pendaftaran_' . $pengajuan->id . '_' . time() . '.pdf';
            $filePath = 'dokumen_form/' . $fileName;
            \Storage::disk('public')->put($filePath, $pdf->output());
            
            \Log::info('Auto-generated Form Permohonan Pendaftaran', [
                'pengajuan_id' => $pengajuan->id,
                'file_path' => $filePath
            ]);

            return $filePath;
        } catch (\Exception $e) {
            \Log::error('Error auto-generating Form Permohonan Pendaftaran', [
                'pengajuan_id' => $pengajuan->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Auto-generate both letters for a pengajuan
     */
    public function autoGenerateBothLetters(PengajuanHki $pengajuan)
    {
        $pengalihanPath = $this->autoGeneratePengalihan($pengajuan);
        $pernyataanPath = $this->autoGeneratePernyataan($pengajuan);

        return [
            'surat_pengalihan' => $pengalihanPath,
            'surat_pernyataan' => $pernyataanPath
        ];
    }

    /**
     * Auto-generate all required documents (form + letters)
     */
    public function autoGenerateAllDocuments(PengajuanHki $pengajuan)
    {
        $formPath = $this->autoGenerateFormPermohonan($pengajuan);
        $pengalihanPath = $this->autoGeneratePengalihan($pengajuan);
        $pernyataanPath = $this->autoGeneratePernyataan($pengajuan);

        return [
            'form_permohonan_pendaftaran' => $formPath,
            'surat_pengalihan' => $pengalihanPath,
            'surat_pernyataan' => $pernyataanPath
        ];
    }
} 