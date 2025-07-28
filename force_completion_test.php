<?php
require_once 'vendor/autoload.php';

// Force completion test
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PengajuanHki;
use App\Models\Signature;
use App\Http\Controllers\MultiSignatureController;
use Illuminate\Support\Facades\Log;

echo "=== FORCE COMPLETION TEST ===\n\n";

// Find pengajuan 78
$pengajuan = PengajuanHki::find(78);
if (!$pengajuan) {
    echo "❌ Pengajuan 78 tidak ditemukan\n";
    exit;
}

echo "✅ Pengajuan ID: {$pengajuan->id}\n";

// Check current signatures
$signatures = Signature::where('pengajuan_hki_id', 78)->get();
echo "📝 Signatures: " . $signatures->count() . "\n";
foreach ($signatures as $sig) {
    echo "   - Pencipta {$sig->pencipta_ke}: {$sig->status}\n";
    echo "     Signature path: " . ($sig->signature_path ?: 'NULL') . "\n";
    echo "     Image path: " . ($sig->signature_image_path ?: 'NULL') . "\n";
}

// Force create overlay manually
echo "\n🔧 FORCING OVERLAY CREATION...\n";

try {
    $controller = new MultiSignatureController();
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('createOverlayDataFromSignatures');
    $method->setAccessible(true);
    
    $method->invoke($controller, $pengajuan);
    
    echo "✅ createOverlayDataFromSignatures executed\n";
    
    // Check if overlay was created
    $pengajuan->refresh();
    $dokumenJson = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];
    
    if (isset($dokumenJson['overlays'])) {
        echo "✅ OVERLAY CREATED!\n";
        foreach ($dokumenJson['overlays'] as $docType => $overlays) {
            echo "   📄 {$docType}: " . count($overlays) . " overlays\n";
        }
    } else {
        echo "❌ Overlay masih tidak ada\n";
        echo "📋 Current dokumen JSON:\n";
        print_r($dokumenJson);
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST SELESAI ===\n"; 