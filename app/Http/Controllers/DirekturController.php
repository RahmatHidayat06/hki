<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class DirekturController extends Controller
{
    // Tampilkan form upload tanda tangan
    public function formTtd()
    {
        $ttdPath = auth()->user()->ttd_path;
        return view('direktur.ttd', compact('ttdPath'));
    }

    // Proses upload tanda tangan
    public function uploadTtd(Request $request)
    {
        $request->validate([
            'ttd' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);
        $file = $request->file('ttd');
        $filename = 'ttd-direktur-' . Auth::id() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('ttd_direktur', $filename, 'public');
        $user = Auth::user();
        $user->ttd_path = $path;
        $user->save();
        return redirect()->route('direktur.ttd.form')->with('success', 'Tanda tangan berhasil diupload!');
    }
} 