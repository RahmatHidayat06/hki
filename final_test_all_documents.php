<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PengajuanHki;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Storage;

echo "=== FINAL TEST - ALL DOCUMENTS ===\n\n";

$pengajuan = PengajuanHki::find(78);
if (!$pengajuan) {
    echo "❌ Pengajuan ID 78 tidak ditemukan\n";
    exit;
}

echo "✅ Pengajuan ID: {$pengajuan->id}\n";
echo "   Judul: {$pengajuan->judul_karya}\n";
echo "   Status: {$pengajuan->status}\n\n";

// Create admin controller instance
$adminController = new AdminController();

// Use reflection to call private method
$reflection = new ReflectionClass($adminController);
$buildDocumentsMethod = $reflection->getMethod('buildDocumentsArray');
$buildDocumentsMethod->setAccessible(true);

// Get documents array
$documents = $buildDocumentsMethod->invoke($adminController, $pengajuan);

echo "📋 DOKUMEN PENDUKUNG STATUS:\n";
foreach ($documents as $key => $docInfo) {
    echo "\n🔸 {$docInfo['label']}:\n";
    echo "   Key: {$key}\n";
    echo "   Description: {$docInfo['description']}\n";
    echo "   Icon: {$docInfo['icon']} (color: {$docInfo['color']})\n";
    
    $fileInfo = $docInfo['file_info'];
    if ($fileInfo && isset($fileInfo['exists']) && $fileInfo['exists']) {
        echo "   Status: ✅ AVAILABLE\n";
        echo "   URL: {$fileInfo['url']}\n";
        echo "   Filename: {$fileInfo['filename']}\n";
        echo "   Extension: {$fileInfo['extension']}\n";
        echo "   Size: " . number_format($fileInfo['size']) . " bytes\n";
        if (isset($fileInfo['is_signed']) && $fileInfo['is_signed']) {
            echo "   🖊️  SIGNED\n";
        }
    } else {
        echo "   Status: ❌ NOT AVAILABLE\n";
    }
    
    if (isset($docInfo['scan_url']) && $docInfo['scan_url']) {
        echo "   QR Link: ✅ Available\n";
    }
}

echo "\n📊 SUMMARY:\n";
$available = 0;
$total = count($documents);
foreach ($documents as $docInfo) {
    if ($docInfo['file_info'] && isset($docInfo['file_info']['exists']) && $docInfo['file_info']['exists']) {
        $available++;
    }
}

echo "   Total Documents: {$total}\n";
echo "   Available: {$available}\n";
echo "   Missing: " . ($total - $available) . "\n";
echo "   Completion Rate: " . round(($available / $total) * 100, 1) . "%\n";

echo "\n=== TEST SELESAI ===\n"; 