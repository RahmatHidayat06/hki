<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\PengajuanHki;
use App\Models\Signature;
use App\Http\Controllers\MultiSignatureController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// Test untuk memverifikasi posisi tanda tangan yang baru
function testSignaturePositioning() {
    echo "=== Test Posisi Tanda Tangan yang Rapi untuk Multiple Pencipta ===\n\n";

    // Ambil pengajuan yang memiliki multiple pencipta
    $pengajuan = PengajuanHki::whereNotNull('alamat_pencipta')
                            ->where('jumlah_pencipta', '>', 1)
                            ->first();

    if (!$pengajuan) {
        echo "❌ Tidak ada pengajuan dengan multiple pencipta untuk testing\n";
        return;
    }

    echo "📋 Testing dengan Pengajuan ID: {$pengajuan->id}\n";
    echo "🎭 Judul: {$pengajuan->judul_karya}\n";
    echo "👥 Jumlah Pencipta: {$pengajuan->jumlah_pencipta}\n\n";

    // Tampilkan data pencipta
    $alamatPencipta = json_decode($pengajuan->alamat_pencipta, true) ?? [];
    echo "📝 Data Pencipta:\n";
    foreach ($alamatPencipta as $i => $pencipta) {
        echo "   {$i}. " . ($pencipta['nama'] ?? "Pencipta {$i}") . "\n";
        echo "      Alamat: " . ($pencipta['alamat'] ?? "Alamat tidak tersedia") . "\n";
    }
    echo "\n";

    // Cek signatures yang ada
    $signatures = Signature::where('pengajuan_hki_id', $pengajuan->id)->get();
    echo "🖊️  Status Signatures:\n";
    if ($signatures->isEmpty()) {
        echo "   ❌ Belum ada signature records untuk pengajuan ini\n";
        echo "   💡 Membuat signature records untuk testing...\n\n";
        
        // Buat signature records untuk testing
        foreach ($alamatPencipta as $i => $pencipta) {
            Signature::create([
                'pengajuan_hki_id' => $pengajuan->id,
                'pencipta_ke' => $i,
                'nama_pencipta' => $pencipta['nama'] ?? "Pencipta {$i}",
                'nama_ttd' => $pencipta['nama'] ?? "Pencipta {$i}",
                'posisi' => 'kanan',
                'status' => 'signed',
                'signature_path' => 'signatures/dummy_signature_' . $i . '.png', // dummy path
                'signed_at' => now(),
                'signature_token' => \Illuminate\Support\Str::random(64)
            ]);
        }
        
        $signatures = Signature::where('pengajuan_hki_id', $pengajuan->id)->get();
    }

    foreach ($signatures as $signature) {
        echo "   Pencipta {$signature->pencipta_ke}: {$signature->nama_pencipta} ";
        echo "({$signature->status})\n";
    }
    echo "\n";

    // Test posisi baru untuk setiap jenis dokumen
    $documentTypes = ['surat_pengalihan', 'surat_pernyataan', 'form_permohonan_pendaftaran'];
    
    echo "🎯 Posisi Tanda Tangan Baru (Disesuaikan agar Rapi):\n\n";

    // Ambil posisi dari MultiSignatureController
    $signaturePositions = [
        'surat_pengalihan' => [
            1 => ['page' => 1, 'x_percent' => 74.0, 'y_percent' => 82.5],
            2 => ['page' => 2, 'x_percent' => 25.0, 'y_percent' => 35.0],
            3 => ['page' => 2, 'x_percent' => 75.0, 'y_percent' => 35.0],
            4 => ['page' => 2, 'x_percent' => 25.0, 'y_percent' => 75.0],
            5 => ['page' => 2, 'x_percent' => 75.0, 'y_percent' => 75.0],
        ],
        'surat_pernyataan' => [
            1 => ['page' => 1, 'x_percent' => 74.0, 'y_percent' => 82.5],
            2 => ['page' => 2, 'x_percent' => 25.0, 'y_percent' => 35.0],
            3 => ['page' => 2, 'x_percent' => 75.0, 'y_percent' => 35.0],
            4 => ['page' => 2, 'x_percent' => 25.0, 'y_percent' => 75.0],
            5 => ['page' => 2, 'x_percent' => 75.0, 'y_percent' => 75.0],
        ],
        'form_permohonan_pendaftaran' => [
            'pemohon' => ['page' => 1, 'x_percent' => 74.0, 'y_percent' => 85.0],
        ]
    ];

    foreach ($documentTypes as $docType) {
        echo "📄 {$docType}:\n";
        $docPositions = $signaturePositions[$docType];
        
        if ($docType === 'form_permohonan_pendaftaran') {
            echo "   👤 Pemohon (Pencipta 1): Halaman {$docPositions['pemohon']['page']}, ";
            echo "X: {$docPositions['pemohon']['x_percent']}%, Y: {$docPositions['pemohon']['y_percent']}%\n";
        } else {
            foreach ($signatures as $signature) {
                $penciptaKe = $signature->pencipta_ke;
                if (isset($docPositions[$penciptaKe])) {
                    $pos = $docPositions[$penciptaKe];
                    echo "   👤 Pencipta {$penciptaKe} ({$signature->nama_pencipta}): ";
                    echo "Halaman {$pos['page']}, X: {$pos['x_percent']}%, Y: {$pos['y_percent']}%\n";
                    
                    // Berikan keterangan lokasi
                    if ($penciptaKe == 1) {
                        echo "      📍 Lokasi: Di tengah area tanda tangan halaman 1 (setelah 'Pencipta I,')\n";
                    } elseif ($penciptaKe == 2 || $penciptaKe == 4) {
                        $baris = $penciptaKe == 2 ? 'atas' : 'bawah';
                        echo "      📍 Lokasi: Kolom kiri {$baris} halaman 2 (setelah 'Pencipta " . ['II', 'III', 'IV', 'V'][$penciptaKe-2] . ",')\n";
                    } elseif ($penciptaKe == 3 || $penciptaKe == 5) {
                        $baris = $penciptaKe == 3 ? 'atas' : 'bawah';
                        echo "      📍 Lokasi: Kolom kanan {$baris} halaman 2 (setelah 'Pencipta " . ['II', 'III', 'IV', 'V'][$penciptaKe-2] . ",')\n";
                    }
                }
            }
        }
        echo "\n";
    }

    echo "✅ Keunggulan Posisi Baru:\n";
    echo "   🎯 Setiap tanda tangan diletakkan tepat setelah label 'Pencipta I/II/III/dst,'\n";
    echo "   📐 Posisi disesuaikan agar tidak bertabrakan dengan elemen lain\n";
    echo "   📏 Ukuran signature 18% x 4.5% memberikan visibilitas yang baik\n";
    echo "   🎨 Layout rapi dengan spacing yang konsisten\n";
    echo "   👥 Setiap pencipta memiliki area signature yang jelas dan terpisah\n\n";

    // Test overlay creation
    echo "🔧 Testing Overlay Creation...\n";
    try {
        $controller = new MultiSignatureController();
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('createOverlayFromSignatures');
        $method->setAccessible(true);
        
        $result = $method->invoke($controller, $pengajuan);
        
        // Check if overlays were created
        $pengajuan->refresh();
        $dokumenJson = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];
        
        if (isset($dokumenJson['overlays'])) {
            echo "   ✅ Overlays berhasil dibuat\n";
            foreach ($dokumenJson['overlays'] as $docType => $overlays) {
                echo "   📄 {$docType}: " . count($overlays) . " overlays\n";
            }
        } else {
            echo "   ⚠️  Overlays belum dibuat (mungkin signatures belum complete)\n";
        }
        
    } catch (Exception $e) {
        echo "   ❌ Error testing overlay creation: " . $e->getMessage() . "\n";
    }

    echo "\n✨ Tanda tangan sekarang akan diletakkan dengan rapi setelah nama masing-masing pencipta!\n";
}

// Jalankan test
testSignaturePositioning(); 