<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image; // ensure intervention installed

class SignatureController extends Controller
{
    /**
     * Simpan tanda tangan (base64 canvas atau file upload) ke storage dan update kolom ttd_path User.
     */
    public function save(Request $request)
    {
        $user = Auth::user();

        // Validasi minimal: harus ada data base64 atau file
        $request->validate([
            'signature_data' => 'nullable|string',
            'signature_file' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
        ]);

        if ($request->filled('signature_data')) {
            $dataUrl = $request->input('signature_data');
            if (!preg_match('/^data:image\/(png|jpe?g);base64,/', $dataUrl)) {
                return response()->json(['message' => 'Format data tidak valid'], 422);
            }
            $extension = (strpos($dataUrl, 'jpeg') !== false || strpos($dataUrl, 'jpg') !== false) ? 'jpg' : 'png';
            $imageData = base64_decode(substr($dataUrl, strpos($dataUrl, ',') + 1));
            $filename = 'signatures/'.Str::uuid().'.'.$extension;
            Storage::disk('public')->put($filename, $imageData);
            $path = $filename;
        } elseif ($request->hasFile('signature_file')) {
            $file = $request->file('signature_file');
            $imageData = file_get_contents($file->getRealPath());
            // buat resource GD
            $src = @imagecreatefromstring($imageData);
            if(!$src){
                return response()->json(['message'=>'Tidak bisa membaca gambar'],422);
            }
            $width = imagesx($src);
            $height = imagesy($src);
            $dest = imagecreatetruecolor($width,$height);
            imagesavealpha($dest,true);
            $transColour = imagecolorallocatealpha($dest,0,0,0,127);
            imagefill($dest,0,0,$transColour);
            // loop pixels
            for($y=0;$y<$height;$y++){
                for($x=0;$x<$width;$x++){
                    $rgba = imagecolorat($src,$x,$y);
                    $r = ($rgba >> 16) & 0xFF;
                    $g = ($rgba >> 8) & 0xFF;
                    $b = $rgba & 0xFF;
                    // threshold white >240
                    if($r>240 && $g>240 && $b>240){
                        // leave transparent
                        continue;
                    }
                    $color = imagecolorallocatealpha($dest,$r,$g,$b,0);
                    imagesetpixel($dest,$x,$y,$color);
                }
            }
            ob_start();
            imagepng($dest);
            $pngData = ob_get_clean();
            imagedestroy($src);
            imagedestroy($dest);
            $filename = 'signatures/'.Str::uuid().'.png';
            Storage::disk('public')->put($filename,$pngData);
            $path = $filename;
        } else {
            return response()->json(['message' => 'Tidak ada data tanda tangan diberikan'], 422);
        }

        // Catatan: mulai sekarang kita tidak menghapus file lama agar direktur bisa menyimpan banyak tanda tangan.
        // Jika ingin membersihkan tanda tangan lama, pengguna dapat menghapusnya secara manual melalui UI.

        $user->ttd_path = $path;
        $user->save();

        return response()->json(['message' => 'Tanda tangan berhasil disimpan', 'url' => Storage::url($path), 'path' => $path]);
    }

    /**
     * Hapus tanda tangan tertentu berdasarkan path yang diberikan melalui request.
     * Path yang dimaksud adalah relative path pada disk "public", mis: signatures/uuid.png
     */
    public function delete(Request $request)
    {
        $request->validate([ 'path' => 'required|string' ]);

        $path = $request->input('path');

        // Cegah directory traversal
        if (strpos($path, '..') !== false) {
            return response()->json(['message' => 'Path tidak valid'], 422);
        }

        if (!Storage::disk('public')->exists($path)) {
            return response()->json(['message' => 'File tidak ditemukan'], 404);
        }

        // Pastikan pengguna memiliki hak untuk menghapus (direktur atau pemilik file)
        $user = Auth::user();
        if ($user->role !== 'direktur') {
            return response()->json(['message' => 'Tidak diizinkan'], 403);
        }

        // Jika signature yang dihapus adalah ttd_path aktif, kosongkan kolom tersebut
        if ($user->ttd_path === $path) {
            $user->ttd_path = null;
            $user->save();
        }

        Storage::disk('public')->delete($path);

        return response()->json(['message' => 'Tanda tangan berhasil dihapus']);
    }
} 