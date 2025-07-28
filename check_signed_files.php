<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PengajuanHki;
use Illuminate\Support\Facades\Storage;

echo "=== CHECK SIGNED FILES ===\n\n";

$pengajuan = PengajuanHki::find(78);
if (!$pengajuan) {
    echo "❌ Pengajuan ID 78 tidak ditemukan\n";
    exit;
}

echo "✅ Pengajuan ID: {$pengajuan->id}\n";
echo "   Judul: {$pengajuan->judul_ciptaan}\n";
echo "   Status: {$pengajuan->status}\n\n";

// Check file_dokumen_pendukung JSON
$dokumen = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];

echo "📄 DOKUMEN JSON:\n";
echo "   Raw: " . substr($pengajuan->file_dokumen_pendukung, 0, 200) . "...\n\n";

echo "🔍 SIGNED FILES CHECK:\n";

// Check each document type
$documentTypes = ['surat_pengalihan', 'surat_pernyataan', 'form_permohonan_pendaftaran'];

foreach ($documentTypes as $docType) {
    echo "\n📋 {$docType}:\n";
    
    // Check in signed array
    if (isset($dokumen['signed'][$docType])) {
        $signedPath = $dokumen['signed'][$docType];
        echo "   ✅ Signed path in JSON: {$signedPath}\n";
        
        // Check if file exists
        $exists = Storage::disk('public')->exists($signedPath);
        echo "   " . ($exists ? "✅" : "❌") . " File exists: " . ($exists ? "YES" : "NO") . "\n";
        
        if ($exists) {
            $fullPath = storage_path('app/public/' . $signedPath);
            $size = filesize($fullPath);
            $modified = filemtime($fullPath);
            echo "   📊 Size: " . number_format($size) . " bytes\n";
            echo "   🕒 Modified: " . date('Y-m-d H:i:s', $modified) . "\n";
        }
    } else {
        echo "   ❌ No signed path in JSON\n";
    }
    
    // Check original path
    if (isset($dokumen[$docType])) {
        $originalPath = $dokumen[$docType];
        echo "   📋 Original path: {$originalPath}\n";
        $exists = Storage::disk('public')->exists($originalPath);
        echo "   " . ($exists ? "✅" : "❌") . " Original exists: " . ($exists ? "YES" : "NO") . "\n";
    }
}

echo "\n🔍 SIGNED_DOCUMENTS FOLDER:\n";
$signedFiles = Storage::disk('public')->files('signed_documents');
if (empty($signedFiles)) {
    echo "   ❌ No files in signed_documents folder\n";
} else {
    foreach ($signedFiles as $file) {
        if (str_contains($file, '78_')) {
            $fullPath = storage_path('app/public/' . $file);
            $size = filesize($fullPath);
            $modified = filemtime($fullPath);
            echo "   ✅ {$file}\n";
            echo "      📊 Size: " . number_format($size) . " bytes\n";
            echo "      🕒 Modified: " . date('Y-m-d H:i:s', $modified) . "\n";
        }
    }
}

echo "\n=== CHECK SELESAI ===\n"; 