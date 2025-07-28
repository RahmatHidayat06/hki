<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PengajuanHki;
use App\Models\Signature;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

echo "=== UPDATE KTP GABUNGAN SYSTEM ===\n\n";

$pengajuan = PengajuanHki::find(78);
if (!$pengajuan) {
    echo "❌ Pengajuan ID 78 tidak ditemukan\n";
    exit;
}

echo "✅ Pengajuan ID: {$pengajuan->id}\n";
echo "   Judul: {$pengajuan->judul_karya}\n\n";

// Check current dokumen structure
$dokumenJson = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];

echo "📋 CURRENT DOKUMEN STRUCTURE:\n";
foreach ($dokumenJson as $key => $value) {
    if ($key === 'overlays' || $key === 'signed') {
        echo "   {$key}: " . count($value) . " items\n";
    } else {
        echo "   {$key}: " . (is_string($value) ? basename($value) : gettype($value)) . "\n";
    }
}

// Check signatures and their KTP files
echo "\n🆔 SIGNATURE KTP STATUS:\n";
$signatures = Signature::where('pengajuan_hki_id', $pengajuan->id)->get();

foreach ($signatures as $signature) {
    echo "   Pencipta {$signature->pencipta_ke}: {$signature->nama_pencipta}\n";
    echo "     Status: {$signature->status}\n";
    if ($signature->ktp_path) {
        $ktpExists = Storage::disk('public')->exists($signature->ktp_path);
        echo "     KTP: " . ($ktpExists ? "✅ {$signature->ktp_path}" : "❌ Missing") . "\n";
    } else {
        echo "     KTP: ❌ No path\n";
    }
}

// Update dokumen structure to include form permohonan in the interface
if (!isset($dokumenJson['form_permohonan_pendaftaran'])) {
    echo "\n📄 FORM PERMOHONAN PENDAFTARAN missing, will be auto-generated\n";
} else {
    echo "\n📄 FORM PERMOHONAN PENDAFTARAN: ✅ {$dokumenJson['form_permohonan_pendaftaran']}\n";
}

// Check if file_karya exists and is accessible
echo "\n🎨 CONTOH CIPTAAN STATUS:\n";
if ($pengajuan->file_karya) {
    // Check if it's a URL or file path
    if (filter_var($pengajuan->file_karya, FILTER_VALIDATE_URL)) {
        echo "   Type: URL - {$pengajuan->file_karya}\n";
        echo "   Status: ✅ External URL\n";
    } else {
        $exists = Storage::disk('public')->exists($pengajuan->file_karya);
        echo "   Type: File - {$pengajuan->file_karya}\n";
        echo "   Status: " . ($exists ? "✅ EXISTS" : "❌ NOT FOUND") . "\n";
        
        if (!$exists) {
            echo "   🔧 File path issue detected\n";
        }
    }
} else {
    echo "   Status: ❌ No file_karya set\n";
}

echo "\n=== UPDATE SELESAI ===\n"; 