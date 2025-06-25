<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PengajuanHki;
use Illuminate\Support\Facades\Storage;

echo "=== DEBUG FILE READING ISSUES ===\n\n";

function debugFileReading($pengajuanId) {
    echo "🔍 Debugging Pengajuan #{$pengajuanId}\n";
    echo str_repeat("=", 50) . "\n";
    
    try {
        $pengajuan = PengajuanHki::find($pengajuanId);
        if (!$pengajuan) {
            echo "❌ Pengajuan tidak ditemukan\n";
            return false;
        }
        
        echo "📋 Status: {$pengajuan->status}\n";
        echo "👤 User: {$pengajuan->user->name}\n";
        
        // Debug file_dokumen_pendukung field
        echo "\n📄 RAW file_dokumen_pendukung:\n";
        var_dump($pengajuan->file_dokumen_pendukung);
        
        // Try to decode JSON
        $dokumen = null;
        if (is_string($pengajuan->file_dokumen_pendukung)) {
            echo "\n🔄 Decoding JSON...\n";
            $dokumen = json_decode($pengajuan->file_dokumen_pendukung, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo "❌ JSON Error: " . json_last_error_msg() . "\n";
            } else {
                echo "✅ JSON decoded successfully\n";
            }
        } elseif (is_array($pengajuan->file_dokumen_pendukung)) {
            echo "\n✅ Already an array\n";
            $dokumen = $pengajuan->file_dokumen_pendukung;
        } else {
            echo "\n⚠️  Field is neither string nor array\n";
            $dokumen = [];
        }
        
        echo "\n📊 DECODED DOKUMEN STRUCTURE:\n";
        print_r($dokumen);
        
        // Test each document type
        $documentTypes = ['dokumen_pengalihan', 'dokumen_pernyataan', 'ktp'];
        
        foreach ($documentTypes as $docType) {
            echo "\n--- Testing {$docType} ---\n";
            
            $path = $dokumen[$docType] ?? null;
            echo "Path from DB: " . ($path ? $path : 'NULL') . "\n";
            
            if ($path) {
                // Test different path variations
                $variations = [
                    'original' => $path,
                    'ltrim_slash' => ltrim($path, '/'),
                    'without_storage' => str_starts_with(ltrim($path, '/'), 'storage/') 
                        ? substr(ltrim($path, '/'), strlen('storage/')) 
                        : ltrim($path, '/'),
                ];
                
                foreach ($variations as $varName => $varPath) {
                    echo "  {$varName}: {$varPath}\n";
                    
                    // Test Storage::disk('public')->exists()
                    $exists = Storage::disk('public')->exists($varPath);
                    echo "    Storage exists: " . ($exists ? "✅ YES" : "❌ NO") . "\n";
                    
                    // Test file_exists()
                    $fullPath = storage_path('app/public/' . $varPath);
                    $fileExists = file_exists($fullPath);
                    echo "    File exists: " . ($fileExists ? "✅ YES" : "❌ NO") . "\n";
                    echo "    Full path: {$fullPath}\n";
                    
                    if ($fileExists) {
                        $size = filesize($fullPath);
                        echo "    File size: " . number_format($size) . " bytes\n";
                        
                        if ($size === 0) {
                            echo "    ⚠️  FILE IS EMPTY!\n";
                        }
                        
                        // Test if file is readable
                        if (is_readable($fullPath)) {
                            echo "    ✅ File is readable\n";
                            
                            // Try to read first 100 bytes
                            $handle = fopen($fullPath, 'r');
                            if ($handle) {
                                $firstBytes = fread($handle, 100);
                                fclose($handle);
                                echo "    First 100 bytes: " . bin2hex(substr($firstBytes, 0, 20)) . "...\n";
                                
                                // Check if it's PDF
                                if (substr($firstBytes, 0, 4) === '%PDF') {
                                    echo "    ✅ Valid PDF file\n";
                                } else {
                                    echo "    ❌ Not a valid PDF file\n";
                                }
                            }
                        } else {
                            echo "    ❌ File is not readable\n";
                        }
                    }
                    echo "\n";
                }
            }
        }
        
        // Test overlay and signed files
        echo "\n🔏 OVERLAY & SIGNED FILES:\n";
        $overlays = $dokumen['overlays'] ?? [];
        $signed = $dokumen['signed'] ?? [];
        
        echo "Overlays structure:\n";
        print_r($overlays);
        
        echo "Signed structure:\n";
        print_r($signed);
        
        foreach (['surat_pengalihan', 'surat_pernyataan'] as $type) {
            if (isset($signed[$type])) {
                $signedPath = $signed[$type];
                echo "\n--- Signed {$type} ---\n";
                echo "Path: {$signedPath}\n";
                
                if (is_array($signedPath)) {
                    echo "⚠️  Signed path is array, taking first element\n";
                    $signedPath = $signedPath[0] ?? null;
                }
                
                if ($signedPath) {
                    $normalizedPath = ltrim($signedPath, '/');
                    if (str_starts_with($normalizedPath, 'storage/')) {
                        $normalizedPath = substr($normalizedPath, strlen('storage/'));
                    }
                    
                    $exists = Storage::disk('public')->exists($normalizedPath);
                    echo "File exists: " . ($exists ? "✅ YES" : "❌ NO") . "\n";
                    echo "Normalized path: {$normalizedPath}\n";
                    
                    if ($exists) {
                        $size = Storage::disk('public')->size($normalizedPath);
                        echo "File size: " . number_format($size) . " bytes\n";
                        
                        if ($size === 0) {
                            echo "⚠️  SIGNED FILE IS EMPTY!\n";
                        }
                    }
                }
            }
        }
        
        return true;
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
        return false;
    }
}

// Test dengan beberapa pengajuan
$pengajuanList = PengajuanHki::whereNotNull('file_dokumen_pendukung')
    ->take(3)
    ->get();

if ($pengajuanList->isEmpty()) {
    echo "⚠️  No pengajuan with file_dokumen_pendukung found\n";
} else {
    foreach ($pengajuanList as $pengajuan) {
        debugFileReading($pengajuan->id);
        echo "\n" . str_repeat("=", 80) . "\n\n";
    }
}

echo "🔧 STORAGE CONFIGURATION:\n";
echo "Storage path: " . storage_path() . "\n";
echo "Public disk path: " . storage_path('app/public') . "\n";
echo "Storage URL: " . Storage::url('') . "\n";

echo "\n📁 CHECKING STORAGE DIRECTORIES:\n";
$directories = ['dokumen_pengalihan', 'dokumen_pernyataan', 'ktp', 'signed_documents'];
foreach ($directories as $dir) {
    $exists = Storage::disk('public')->exists($dir);
    echo "{$dir}: " . ($exists ? "✅ EXISTS" : "❌ MISSING") . "\n";
    
    if ($exists) {
        $files = Storage::disk('public')->files($dir);
        echo "  Files count: " . count($files) . "\n";
        
        // Show first 3 files
        foreach (array_slice($files, 0, 3) as $file) {
            $size = Storage::disk('public')->size($file);
            echo "  - {$file} (" . number_format($size) . " bytes)\n";
        }
    }
}

?> 