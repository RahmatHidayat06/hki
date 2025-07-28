<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PengajuanHki;
use App\Models\Signature;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\PdfSigningController;
use App\Http\Controllers\SuratController;
use Illuminate\Support\Facades\Log;

echo "=== DEBUG AUTO-SIGN PROCESS ===\n\n";

$pengajuan = PengajuanHki::find(78);
if (!$pengajuan) {
    echo "❌ Pengajuan ID 78 tidak ditemukan\n";
    exit;
}

echo "✅ Pengajuan ID: {$pengajuan->id}\n";
echo "   Status: {$pengajuan->status}\n\n";

// Check if all signatures are complete
$totalSignatures = Signature::where('pengajuan_hki_id', $pengajuan->id)->count();
$signedSignatures = Signature::where('pengajuan_hki_id', $pengajuan->id)->where('status', 'signed')->count();

echo "📝 SIGNATURE STATUS:\n";
echo "   Total signatures: {$totalSignatures}\n";
echo "   Signed signatures: {$signedSignatures}\n";
echo "   All complete: " . ($totalSignatures > 0 && $totalSignatures === $signedSignatures ? "YES" : "NO") . "\n\n";

if ($totalSignatures > 0 && $totalSignatures === $signedSignatures) {
    echo "🔧 MANUAL AUTO-SIGN PROCESS:\n";
    
    // Get current dokumen
    $dokumenJson = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];
    $overlays = $dokumenJson['overlays'] ?? [];
    
    echo "   Current overlays: " . json_encode(array_keys($overlays)) . "\n";
    
    // Check if letters exist, if not generate them
    $suratController = new SuratController();
    
    if (!isset($dokumenJson['surat_pengalihan'])) {
        echo "   📄 Generating surat pengalihan...\n";
        $pengalihanPath = $suratController->autoGeneratePengalihan($pengajuan);
        if ($pengalihanPath) {
            $dokumenJson['surat_pengalihan'] = $pengalihanPath;
            echo "   ✅ Surat pengalihan generated: {$pengalihanPath}\n";
        } else {
            echo "   ❌ Failed to generate surat pengalihan\n";
        }
    } else {
        echo "   ✅ Surat pengalihan exists: {$dokumenJson['surat_pengalihan']}\n";
    }
    
    if (!isset($dokumenJson['surat_pernyataan'])) {
        echo "   📄 Generating surat pernyataan...\n";
        $pernyataanPath = $suratController->autoGeneratePernyataan($pengajuan);
        if ($pernyataanPath) {
            $dokumenJson['surat_pernyataan'] = $pernyataanPath;
            echo "   ✅ Surat pernyataan generated: {$pernyataanPath}\n";
        } else {
            echo "   ❌ Failed to generate surat pernyataan\n";
        }
    } else {
        echo "   ✅ Surat pernyataan exists: {$dokumenJson['surat_pernyataan']}\n";
    }
    
    // Update pengajuan with new letters
    $pengajuan->update(['file_dokumen_pendukung' => json_encode($dokumenJson)]);
    echo "   ✅ Updated pengajuan with letters\n";
    
    // Now try to sign PDFs
    $pdfSigner = new PdfSigningController();
    
    foreach (['surat_pengalihan', 'surat_pernyataan'] as $docType) {
        echo "\n   🖊️  Attempting to sign {$docType}:\n";
        
        $hasOverlay = isset($overlays[$docType]) && !empty($overlays[$docType]);
        $hasDocument = isset($dokumenJson[$docType]);
        
        echo "      - Has overlay: " . ($hasOverlay ? "YES" : "NO") . "\n";
        echo "      - Has document: " . ($hasDocument ? "YES" : "NO") . "\n";
        
        if ($hasOverlay && $hasDocument) {
            echo "      - Signing PDF...\n";
            $signedPath = $pdfSigner->signPdf($pengajuan, $docType);
            if ($signedPath) {
                echo "      ✅ PDF signed successfully: {$signedPath}\n";
            } else {
                echo "      ❌ PDF signing failed\n";
            }
        } else {
            echo "      ⚠️  Skipping - missing overlay or document\n";
        }
    }
    
} else {
    echo "⚠️  Not all signatures complete, auto-sign won't trigger\n";
}

echo "\n=== DEBUG SELESAI ===\n"; 