<?php
require_once 'vendor/autoload.php';

// Test script untuk memverifikasi alur tanda tangan
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PengajuanHki;
use App\Models\Signature;
use Illuminate\Support\Facades\Log;

echo "=== TEST ALUR TANDA TANGAN ===\n\n";

// 1. Cari pengajuan dengan signature
$pengajuanWithSignatures = PengajuanHki::whereHas('signatures')->with('signatures')->first();

if (!$pengajuanWithSignatures) {
    echo "❌ Tidak ada pengajuan dengan signature records\n";
    exit;
}

echo "✅ Pengajuan ID: {$pengajuanWithSignatures->id}\n";
echo "   Judul: {$pengajuanWithSignatures->judul_karya}\n";

// 2. Check signatures
$signatures = $pengajuanWithSignatures->signatures;
$totalSignatures = $signatures->count();
$signedCount = $signatures->where('status', 'signed')->count();

echo "\n📝 SIGNATURE STATUS:\n";
echo "   Total signatures: {$totalSignatures}\n";
echo "   Signed: {$signedCount}\n";
echo "   Pending: " . ($totalSignatures - $signedCount) . "\n";

foreach ($signatures as $sig) {
    echo "   - Pencipta {$sig->pencipta_ke}: {$sig->status} ({$sig->nama_pencipta})\n";
}

// 3. Check completion
$allComplete = Signature::allSignedForPengajuan($pengajuanWithSignatures->id);
echo "\n🎯 COMPLETION CHECK: " . ($allComplete ? "✅ LENGKAP" : "❌ BELUM LENGKAP") . "\n";

// 4. Check overlay data
$dokumenJson = json_decode($pengajuanWithSignatures->file_dokumen_pendukung, true) ?? [];
echo "\n📋 DOKUMEN DATA:\n";

if (isset($dokumenJson['surat_pengalihan'])) {
    echo "   ✅ Surat Pengalihan: {$dokumenJson['surat_pengalihan']}\n";
} else {
    echo "   ❌ Surat Pengalihan: TIDAK ADA\n";
}

if (isset($dokumenJson['surat_pernyataan'])) {
    echo "   ✅ Surat Pernyataan: {$dokumenJson['surat_pernyataan']}\n";
} else {
    echo "   ❌ Surat Pernyataan: TIDAK ADA\n";
}

// 5. Check overlays
if (isset($dokumenJson['overlays'])) {
    echo "\n🎨 OVERLAY DATA:\n";
    foreach ($dokumenJson['overlays'] as $docType => $overlays) {
        echo "   📄 {$docType}: " . count($overlays) . " overlays\n";
        foreach ($overlays as $i => $overlay) {
            echo "      - Overlay " . ($i+1) . ": {$overlay['type']} di halaman {$overlay['page']}\n";
            echo "        Posisi: ({$overlay['x_percent']}%, {$overlay['y_percent']}%)\n";
            echo "        Ukuran: {$overlay['width_percent']}% x {$overlay['height_percent']}%\n";
        }
    }
} else {
    echo "\n❌ OVERLAY DATA: TIDAK ADA\n";
}

// 6. Check signed documents
if (isset($dokumenJson['signed'])) {
    echo "\n📑 SIGNED DOCUMENTS:\n";
    foreach ($dokumenJson['signed'] as $docType => $path) {
        $exists = \Illuminate\Support\Facades\Storage::disk('public')->exists($path);
        echo "   📄 {$docType}: " . ($exists ? "✅ ADA" : "❌ TIDAK ADA") . " ({$path})\n";
    }
} else {
    echo "\n❌ SIGNED DOCUMENTS: TIDAK ADA\n";
}

echo "\n=== TEST SELESAI ===\n"; 