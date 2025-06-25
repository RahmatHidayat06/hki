<?php

namespace App\Http\Controllers;

use App\Models\PengajuanHki;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

class PengajuanController extends Controller
{
    protected $redirector;

    public function __construct(Redirector $redirector)
    {
        $this->middleware('auth');
        $this->authorizeResource(PengajuanHki::class, 'pengajuan');
        $this->redirector = $redirector;
    }

    public function index(Request $request)
    {
        $query = PengajuanHki::query();

        // Batasi hanya pengajuan milik dosen/mahasiswa yang login
        if (in_array(Auth::user()->role, ['dosen', 'mahasiswa'])) {
            $query->where('user_id', Auth::id());
        }

        // Pencarian global
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('judul_karya', 'like', "%{$search}%")
                  ->orWhere('nama_pengusul', 'like', "%{$search}%")
                  ->orWhere('nomor_pengajuan', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Rentang tanggal dibuat
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Sorting
        switch ($request->input('sort')) {
            case 'oldest':
                $query->oldest();
                break;
            case 'title':
                $query->orderBy('judul_karya');
                break;
            default: // newest
                $query->latest();
        }

        $pengajuan = $query->paginate(10)->appends($request->query());

        return view('pengajuan.index', compact('pengajuan'));
    }

    public function create()
    {
        return view('pengajuan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'jenis_hki' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'file_pengajuan' => 'required|file|mimes:pdf|max:10240',
        ]);

        $file = $request->file('file_pengajuan');
        $path = $file->store('pengajuan', 'public');

        $pengajuan = PengajuanHki::create([
            'user_id' => Auth::id(),
            'judul' => $validated['judul'],
            'jenis_hki' => $validated['jenis_hki'],
            'deskripsi' => $validated['deskripsi'],
            'file_path' => $path,
            'status' => 'pending',
        ]);

        return $this->redirector->route('pengajuan.show', $pengajuan)
            ->with('success', 'Pengajuan berhasil dibuat.');
    }

    public function show(PengajuanHki $pengajuan)
    {
        return view('pengajuan.show', compact('pengajuan'));
    }

    public function edit(PengajuanHki $pengajuan)
    {
        return view('pengajuan.edit', compact('pengajuan'));
    }

    public function update(Request $request, PengajuanHki $pengajuan): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'jenis_hki' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'file_pengajuan' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('file_pengajuan')) {
            $file = $request->file('file_pengajuan');
            $path = $file->store('pengajuan', 'public');
            $validated['file_path'] = $path;
        }

        $pengajuan->update($validated);

        return $this->redirector->route('pengajuan.show', $pengajuan)
            ->with('success', 'Pengajuan berhasil diperbarui.');
    }

    public function destroy(PengajuanHki $pengajuan): RedirectResponse
    {
        $pengajuan->delete();

        return $this->redirector->route('pengajuan.index')
            ->with('success', 'Pengajuan berhasil dihapus.');
    }
} 