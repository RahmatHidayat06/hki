<?php

namespace App\Http\Controllers;

use App\Models\QrLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class DocumentController extends Controller
{
    public function serve(Request $request, string $hash)
    {
        $link = QrLink::where('hash', $hash)->firstOrFail();

        $path = $link->path;
        // If path is full URL (http) just redirect with preview or download modifications
        if(str_starts_with($path, 'http')){
            return $this->handleExternal($path, $request);
        }

        // Assume stored in storage/app/public
        if(!Storage::disk('public')->exists($path)){
            abort(404, 'Dokumen tidak ditemukan');
        }

        $absUrl = URL::to(Storage::url($path));
        return $this->handleExternal($absUrl, $request, storage_path('app/public/'.$path));
    }

    private function handleExternal(string $url, Request $request, ?string $localPath = null)
    {
        $mode = $request->query('mode', 'preview');
        $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));

        if($mode === 'download'){
            // If local path available serve download, else redirect with header
            if($localPath && file_exists($localPath)){
                return response()->download($localPath);
            }
            return redirect()->away($url); // fallback download via browser
        }

        // Preview mode
        if(in_array($ext, ['pdf','doc','docx','ppt','pptx','xls','xlsx'])){
            $viewerUrl = 'https://docs.google.com/viewerng/viewer?embedded=1&url='.urlencode($url);
            return redirect()->away($viewerUrl);
        }

        // For images display inline
        if($localPath && in_array($ext, ['png','jpg','jpeg','gif','webp'])){
            return response()->file($localPath);
        }

        // Default: redirect to download page
        return view('dokumen.show', [
            'url' => $url,
            'ext' => strtoupper($ext),
        ]);
    }
} 