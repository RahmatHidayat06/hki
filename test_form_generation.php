<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PengajuanHki;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\MultiSignatureController;
use App\Http\Controllers\PdfSigningController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

echo "=== TEST FORM GENERATION ===\n\n";

$pengajuan = PengajuanHki::find(78);
if (!$pengajuan) {
    echo "❌ Pengajuan ID 78 tidak ditemukan\n";
    exit;
}

echo "✅ Pengajuan ID: {$pengajuan->id}\n";
echo "   Judul: {$pengajuan->judul_karya}\n\n";

// Test auto-generate form
echo "📄 GENERATING FORM PERMOHONAN PENDAFTARAN:\n";

$suratController = new SuratController();
$formPath = $suratController->autoGenerateFormPermohonan($pengajuan);

if ($formPath) {
    echo "   ✅ Form generated: {$formPath}\n";
    
    // Check if file exists
    $exists = Storage::disk('public')->exists($formPath);
    echo "   ✅ File exists: " . ($exists ? "YES" : "NO") . "\n";
    
    if ($exists) {
        $fullPath = storage_path('app/public/' . $formPath);
        $size = filesize($fullPath);
        echo "   📊 Size: " . number_format($size) . " bytes\n";
    }
} else {
    echo "   ❌ Failed to generate form\n";
}

// Update pengajuan dengan form path
$dokumenJson = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];
if ($formPath) {
    $dokumenJson['form_permohonan_pendaftaran'] = $formPath;
    $pengajuan->update(['file_dokumen_pendukung' => json_encode($dokumenJson)]);
    echo "   ✅ Updated pengajuan with form path\n";
}

// Check current overlay status
echo "\n🖊️  CHECKING CURRENT OVERLAY STATUS:\n";

$pengajuan->refresh();
$currentDokumen = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];
$currentOverlays = $currentDokumen['overlays'] ?? [];

echo "   Current overlay keys: " . implode(', ', array_keys($currentOverlays)) . "\n";

$formOverlays = $currentOverlays['form_permohonan_pendaftaran'] ?? [];
echo "   Form overlays: " . count($formOverlays) . "\n";

if (!empty($formOverlays)) {
    foreach ($formOverlays as $i => $overlay) {
        echo "   Overlay {$i}: page {$overlay['page']}, x:{$overlay['x_percent']}%, y:{$overlay['y_percent']}%\n";
    }
    
    // Test PDF signing for form
    echo "\n   🔧 SIGNING FORM PDF:\n";
    $pdfSigner = new PdfSigningController();
    $signedFormPath = $pdfSigner->signPdf($pengajuan, 'form_permohonan_pendaftaran');
    
    if ($signedFormPath) {
        echo "   ✅ Form PDF signed: {$signedFormPath}\n";
        
        // Check signed file
        $signedExists = Storage::disk('public')->exists($signedFormPath);
        echo "   ✅ Signed file exists: " . ($signedExists ? "YES" : "NO") . "\n";
        
        if ($signedExists) {
            $signedFullPath = storage_path('app/public/' . $signedFormPath);
            $signedSize = filesize($signedFullPath);
            echo "   📊 Signed size: " . number_format($signedSize) . " bytes\n";
        }
    } else {
        echo "   ❌ Failed to sign form PDF\n";
    }
} else {
    echo "   ⚠️  No overlays available for form - form will be signed when signatures are complete\n";
    echo "   💡 Form berhasil dibuat dan akan otomatis ditandatangani saat signature flow selesai\n";
}

echo "\n=== TEST SELESAI ===\n"; 