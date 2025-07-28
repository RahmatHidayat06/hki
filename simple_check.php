<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PengajuanHki;
use Illuminate\Support\Facades\Storage;

$pengajuan = PengajuanHki::find(78);

echo "ID: " . $pengajuan->id . "\n";
echo "Status: " . $pengajuan->status . "\n";
echo "JSON Length: " . strlen($pengajuan->file_dokumen_pendukung) . "\n";

$dokumen = json_decode($pengajuan->file_dokumen_pendukung, true);

echo "\nDokumen keys: " . implode(', ', array_keys($dokumen)) . "\n";

if (isset($dokumen['signed'])) {
    echo "\nSigned keys: " . implode(', ', array_keys($dokumen['signed'])) . "\n";
    
    foreach ($dokumen['signed'] as $type => $path) {
        echo "\n{$type}: {$path}\n";
        echo "Exists: " . (Storage::disk('public')->exists($path) ? "YES" : "NO") . "\n";
    }
} else {
    echo "\nNo 'signed' key found\n";
}

// Check signed_documents folder
echo "\nFiles in signed_documents:\n";
$files = Storage::disk('public')->files('signed_documents');
foreach ($files as $file) {
    if (str_contains($file, '78')) {
        echo "- {$file}\n";
    }
} 