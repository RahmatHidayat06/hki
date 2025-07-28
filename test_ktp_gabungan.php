<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PengajuanHki;
use App\Http\Controllers\MultiSignatureController;
use Illuminate\Support\Facades\Storage;

echo "=== TEST KTP GABUNGAN ===\n\n";

$pengajuan = PengajuanHki::find(78);
if (!$pengajuan) {
    echo "❌ Pengajuan ID 78 tidak ditemukan\n";
    exit;
}

echo "✅ Pengajuan ID: {$pengajuan->id}\n";
echo "   Judul: {$pengajuan->judul_karya}\n\n";

// Test KTP gabungan generation
echo "🆔 GENERATING KTP GABUNGAN:\n";

$multiSignController = new MultiSignatureController();
$ktpGabunganPath = $multiSignController->generateCombinedKtpDocument($pengajuan->id);

if ($ktpGabunganPath) {
    echo "   ✅ KTP Gabungan generated: {$ktpGabunganPath}\n";
    
    // Check if file exists
    $exists = Storage::disk('public')->exists($ktpGabunganPath);
    echo "   ✅ File exists: " . ($exists ? "YES" : "NO") . "\n";
    
    if ($exists) {
        $fullPath = storage_path('app/public/' . $ktpGabunganPath);
        $size = filesize($fullPath);
        echo "   📊 Size: " . number_format($size) . " bytes\n";
    }
} else {
    echo "   ❌ Failed to generate KTP Gabungan\n";
}

// Check current dokumen structure
echo "\n📋 UPDATED DOKUMEN STRUCTURE:\n";
$pengajuan->refresh();
$dokumenJson = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];

foreach ($dokumenJson as $key => $value) {
    if ($key === 'overlays' || $key === 'signed') {
        echo "   {$key}: " . count($value) . " items\n";
    } else {
        echo "   {$key}: " . (is_string($value) ? basename($value) : gettype($value)) . "\n";
    }
}

echo "\n=== TEST SELESAI ===\n"; 