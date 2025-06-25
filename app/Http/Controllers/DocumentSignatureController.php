<?php

namespace App\Http\Controllers;

use App\Models\PengajuanHki;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Spatie\PdfToImage\Pdf;

class DocumentSignatureController extends Controller
{
    /**
     * Show the document signature overlay interface
     */
    public function index(PengajuanHki $pengajuan)
    {
        // Check if user has permission to access this pengajuan
        if (!in_array(auth()->user()->role, ['direktur', 'admin']) && auth()->user()->id !== $pengajuan->user_id) {
            abort(403, 'Unauthorized access');
        }

        // Get available signatures and stamps
        $signatures = $this->getAvailableSignatures();
        $stamps = $this->getAvailableStamps();
        
        // Get uploaded documents
        $documents = $this->getUploadedDocuments($pengajuan);

        return view('document-signature.index', compact('pengajuan', 'signatures', 'stamps', 'documents'));
    }

    /**
     * Show document with overlay interface
     */
    public function show(PengajuanHki $pengajuan, $documentType)
    {
        if (!in_array($documentType, ['surat_pengalihan', 'surat_pernyataan'])) {
            abort(404, 'Jenis dokumen tidak valid.');
        }

        $documentPath = $this->getDocumentPath($pengajuan, $documentType);
        
        if (!$documentPath || !Storage::disk('public')->exists($documentPath)) {
            return redirect()->back()->with('error', 'File dokumen tidak ditemukan.');
        }

        // Hapus logika pembuatan pratinjau
        $documentUrl = Storage::url($documentPath);

        $signatures = $this->getAvailableSignatures();
        $stamps = $this->getAvailableStamps();
        $dokumenPendukung = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];
        $overlays = $dokumenPendukung[$documentType . '_overlay'] ?? [];

        return view('document-signature.editor', compact(
            'pengajuan', 
            'documentType', 
            'documentPath', 
            'documentUrl', 
            'signatures', 
            'stamps',
            'overlays'
        ));
    }

    /**
     * Apply signature and stamp overlay to document
     */
    public function applyOverlay(Request $request, PengajuanHki $pengajuan, $documentType)
    {
        $request->validate([
            'signature_id' => 'required|string',
            'stamp_id' => 'nullable|string',
            'signature_x' => 'required|numeric|min:0',
            'signature_y' => 'required|numeric|min:0',
            'signature_width' => 'required|numeric|min:10|max:200',
            'signature_height' => 'required|numeric|min:10|max:200',
            'stamp_x' => 'nullable|numeric|min:0',
            'stamp_y' => 'nullable|numeric|min:0',
            'stamp_width' => 'nullable|numeric|min:10|max:200',
            'stamp_height' => 'nullable|numeric|min:10|max:200',
        ]);

        try {
            // Get original document path
            $originalPath = $this->getDocumentPath($pengajuan, $documentType);
            
            if (!$originalPath || !Storage::disk('public')->exists($originalPath)) {
                return response()->json(['error' => 'Dokumen tidak ditemukan'], 404);
            }

            // For now, we'll save the overlay information to database
            // Later this can be enhanced to actually merge the PDF
            $overlayData = [
                'signature_id' => $request->signature_id,
                'stamp_id' => $request->stamp_id,
                'signature_position' => [
                    'x' => $request->signature_x,
                    'y' => $request->signature_y,
                    'width' => $request->signature_width,
                    'height' => $request->signature_height,
                ],
                'stamp_position' => $request->stamp_id ? [
                    'x' => $request->stamp_x,
                    'y' => $request->stamp_y,
                    'width' => $request->stamp_width,
                    'height' => $request->stamp_height,
                ] : null,
                'applied_at' => now(),
                'applied_by' => auth()->id()
            ];

            // Update pengajuan with overlay information
            $this->updatePengajuanWithOverlayData($pengajuan, $documentType, $overlayData);

            return response()->json([
                'success' => true,
                'message' => 'Informasi tanda tangan dan materai berhasil disimpan',
                'overlay_data' => $overlayData
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get available signatures
     */
    private function getAvailableSignatures()
    {
        $signatures = [];
        
        // Get director's signature if exists
        if (auth()->user()->role === 'direktur' && auth()->user()->ttd_path) {
            $signatures[] = [
                'id' => 'director_signature',
                'name' => 'Tanda Tangan Direktur',
                'path' => auth()->user()->ttd_path,
                'url' => Storage::url(auth()->user()->ttd_path)
            ];
        }

        // Get digital signatures from validation process
        $digitalSignatures = Storage::disk('public')->files('signatures');
        foreach ($digitalSignatures as $signature) {
            $signatures[] = [
                'id' => basename($signature, '.png'),
                'name' => 'Tanda Tangan Digital - ' . basename($signature, '.png'),
                'path' => $signature,
                'url' => Storage::url($signature)
            ];
        }

        return $signatures;
    }

    /**
     * Get available stamps/materai
     */
    private function getAvailableStamps()
    {
        $stamps = [];
        
        // Get uploaded materai
        $materaiFiles = Storage::disk('public')->files('matrai');
        foreach ($materaiFiles as $materai) {
            $stamps[] = [
                'id' => basename($materai),
                'name' => 'Materai - ' . basename($materai),
                'path' => $materai,
                'url' => Storage::url($materai)
            ];
        }

        // Add default materai if exists
        if (file_exists(public_path('img/materai10rb.png'))) {
            $stamps[] = [
                'id' => 'default_materai',
                'name' => 'Materai 10.000',
                'path' => 'img/materai10rb.png',
                'url' => asset('img/materai10rb.png')
            ];
        }

        return $stamps;
    }

    /**
     * Get uploaded documents for pengajuan
     */
    private function getUploadedDocuments($pengajuan)
    {
        $documents = [];
        
        if ($pengajuan->file_dokumen_pendukung) {
            $dokumenPendukung = json_decode($pengajuan->file_dokumen_pendukung, true);
            
            if (isset($dokumenPendukung['surat_pengalihan'])) {
                $documents['surat_pengalihan'] = [
                    'name' => 'Surat Pengalihan',
                    'path' => $dokumenPendukung['surat_pengalihan'],
                    'url' => Storage::url($dokumenPendukung['surat_pengalihan'])
                ];
            }
            
            if (isset($dokumenPendukung['surat_pernyataan'])) {
                $documents['surat_pernyataan'] = [
                    'name' => 'Surat Pernyataan',
                    'path' => $dokumenPendukung['surat_pernyataan'],
                    'url' => Storage::url($dokumenPendukung['surat_pernyataan'])
                ];
            }
        }

        return $documents;
    }

    /**
     * Get document path by type
     */
    private function getDocumentPath($pengajuan, $documentType)
    {
        if (!$pengajuan->file_dokumen_pendukung) {
            return null;
        }

        $dokumenPendukung = json_decode($pengajuan->file_dokumen_pendukung, true);
        return $dokumenPendukung[$documentType] ?? null;
    }

    /**
     * Update pengajuan with overlay data
     */
    private function updatePengajuanWithOverlayData($pengajuan, $documentType, $overlayData)
    {
        $dokumenPendukung = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];
        $dokumenPendukung[$documentType . '_overlay'] = $overlayData;
        
        $pengajuan->update([
            'file_dokumen_pendukung' => json_encode($dokumenPendukung)
        ]);
    }

    /**
     * Generate signed document preview (for future implementation)
     */
    public function generatePreview(PengajuanHki $pengajuan, $documentType)
    {
        // This method can be implemented later to generate actual PDF with overlays
        // For now, it returns the overlay data
        
        $documentPath = $this->getDocumentPath($pengajuan, $documentType);
        $dokumenPendukung = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];
        $overlayData = $dokumenPendukung[$documentType . '_overlay'] ?? null;
        
        return response()->json([
            'document_path' => $documentPath,
            'overlay_data' => $overlayData
        ]);
    }

    public function showSignatureEditor($id)
    {
        $pengajuan = PengajuanHki::findOrFail($id);
        $document = $pengajuan->dokumen->first(); 
        if (!$document) {
            abort(404, 'Dokumen relasi tidak ditemukan.');
        }
        $documentPath = $document->file_path;

        if (!$documentPath || !Storage::disk('public')->exists($documentPath)) {
            abort(404, 'File dokumen tidak ditemukan di storage.');
        }

        // Hapus logika pembuatan pratinjau
        $documentUrl = Storage::url($documentPath);

        $signatures = $this->getAvailableSignatures();
        $stamps = $this->getAvailableStamps();
        $documentType = $pengajuan->surat_pernyataan ? 'surat_pernyataan' : 'surat_pengalihan';
        $overlays = $pengajuan->{$documentType . '_overlay'} ?? [];


        return view('documents.signature-editor', compact(
            'pengajuan',
            'documentUrl',
            'documentPath',
            'signatures',
            'stamps',
            'documentType',
            'overlays'
        ));
    }

    public function saveSignature(Request $request, $id)
    {
        // Implementasi metode saveSignature
    }
} 