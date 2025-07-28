<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PengajuanHki;
use Illuminate\Support\Facades\Storage;

echo "=== TEST CONTOH CIPTAAN ACCESS ===\n\n";

$pengajuan = PengajuanHki::find(78);
if (!$pengajuan) {
    echo "❌ Pengajuan ID 78 tidak ditemukan\n";
    exit;
}

echo "✅ Pengajuan ID: {$pengajuan->id}\n";
echo "   Judul: {$pengajuan->judul_karya}\n\n";

echo "🎨 CONTOH CIPTAAN STATUS:\n";
$fileKarya = $pengajuan->file_karya;
echo "   Raw path: {$fileKarya}\n";

if ($fileKarya) {
    // Check if it's a URL or file path
    if (filter_var($fileKarya, FILTER_VALIDATE_URL)) {
        echo "   Type: URL\n";
        echo "   Status: ✅ External URL - {$fileKarya}\n";
    } else {
        echo "   Type: File path\n";
        
        // Test different path formats
        $paths = [
            $fileKarya,
            ltrim($fileKarya, '/'),
            'storage/' . ltrim($fileKarya, '/'),
        ];
        
        foreach ($paths as $i => $path) {
            $exists = Storage::disk('public')->exists($path);
            echo "   Path {$i}: '{$path}' - " . ($exists ? "✅ EXISTS" : "❌ NOT FOUND") . "\n";
            
            if ($exists) {
                $url = Storage::url($path);
                echo "      URL: {$url}\n";
                
                $fullPath = storage_path('app/public/' . $path);
                if (file_exists($fullPath)) {
                    $size = filesize($fullPath);
                    echo "      Size: " . number_format($size) . " bytes\n";
                    $ext = strtoupper(pathinfo($path, PATHINFO_EXTENSION));
                    echo "      Extension: {$ext}\n";
                }
            }
        }
    }
} else {
    echo "   Status: ❌ No file_karya set\n";
}

echo "\n=== TEST SELESAI ===\n"; 