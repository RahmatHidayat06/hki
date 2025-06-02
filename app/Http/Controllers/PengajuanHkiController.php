<?php

namespace App\Http\Controllers;

use App\Models\PengajuanHki;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PengajuanHkiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        $pengajuan = PengajuanHki::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('pengajuan.index', compact('pengajuan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pengajuan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Jika tombol simpan draft ditekan
        if ($request->has('save_as_draft')) {
            $validated = $request->validate([
                'judul' => 'nullable|string|max:255',
                'kategori' => 'nullable|string|max:255',
                'deskripsi' => 'nullable|string',
                'nama_pengusul' => 'nullable|string|max:255',
                'nip_nidn' => 'nullable|string|max:255',
                'no_hp' => 'nullable|string|max:255',
                'id_sinta' => 'nullable|string|max:255',
                'jumlah_pencipta' => 'nullable|string|in:1 orang,2 orang,3 orang,4 orang,5 orang',
                'identitas_ciptaan' => 'nullable|string|in:karya tulis,karya audio visual,karya lainnya',
                'sub_jenis_ciptaan' => 'nullable|string',
                'tanggal_pertama_kali_diumumkan' => 'nullable|date',
                'tahun_usulan' => 'nullable|string|max:10',
                'role' => 'nullable|in:dosen,mahasiswa',
                // dokumen dan pencipta dinamis tidak required saat draft
            ]);
            $pengajuan = PengajuanHki::create([
                'user_id' => Auth::id(),
                'judul_karya' => $validated['judul'] ?? null,
                'kategori' => $validated['kategori'] ?? null,
                'deskripsi' => $validated['deskripsi'] ?? null,
                'file_karya' => null,
                'file_dokumen_pendukung' => null,
                'status' => 'draft',
                'catatan_validasi' => null,
                'catatan_persetujuan' => null,
                'nomor_pengajuan' => null,
                'tanggal_pengajuan' => Carbon::now('Asia/Makassar'),
                'nama_pengusul' => $validated['nama_pengusul'] ?? null,
                'nip_nidn' => $validated['nip_nidn'] ?? null,
                'no_hp' => $validated['no_hp'] ?? null,
                'id_sinta' => $validated['id_sinta'] ?? null,
                'jumlah_pencipta' => $validated['jumlah_pencipta'] ?? null,
                'identitas_ciptaan' => $validated['identitas_ciptaan'] ?? null,
                'sub_jenis_ciptaan' => $validated['sub_jenis_ciptaan'] ?? null,
                'tanggal_pertama_kali_diumumkan' => $validated['tanggal_pertama_kali_diumumkan'] ?? null,
                'tahun_usulan' => $validated['tahun_usulan'] ?? null,
                'role' => $validated['role'] ?? null,
            ]);
            // Simpan data pencipta dinamis jika ada
            if ($request->has('pencipta')) {
                foreach ($request->input('pencipta') as $dataPencipta) {
                    if (!empty($dataPencipta['email']) && !empty($dataPencipta['nama'])) {
                        $pengajuan->pengaju()->create([
                            'nama' => $dataPencipta['nama'],
                            'email' => $dataPencipta['email'],
                            'no_hp' => $dataPencipta['no_hp'] ?? null,
                            'alamat' => $dataPencipta['alamat'] ?? null,
                            'kecamatan' => $dataPencipta['kecamatan'] ?? null,
                            'kodepos' => $dataPencipta['kodepos'] ?? null,
                        ]);
                    }
                }
            }
            return Redirect::to(route('draft.index'))->with('success', 'Draft berhasil disimpan');
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'nama_pengusul' => 'required|string|max:255',
            'nip_nidn' => ['nullable', 'string', 'max:255', 'required_if:role,dosen'],
            'no_hp' => 'required|string|max:255',
            'id_sinta' => 'required|string|max:255',
            'jumlah_pencipta' => 'required|string|in:1 orang,2 orang,3 orang,4 orang,5 orang',
            'identitas_ciptaan' => 'required|string|in:karya tulis,karya audio visual,karya lainnya',
            'sub_jenis_ciptaan' => 'required|string|in:Buku,E-Book,Diktat,Modul,Buku Panduan/Petunjuk,Karya Ilmiah,Karya Tulis/Artikel,Laporan Penelitian,Jurnal,Kuliah,Karya Rekaman Video,Karya Siaran Video,Program Komputer,Permainan Video,Basis Data',
            'tanggal_pertama_kali_diumumkan' => 'required|date',
            'contoh_ciptaan_type' => 'required|in:upload,link',
            'contoh_ciptaan' => 'required_if:contoh_ciptaan_type,upload|file|mimes:pdf,mp4,mp3,jpg,jpeg,png,gif,svg,webp,doc,docx|max:10240',
            'contoh_ciptaan_link' => 'required_if:contoh_ciptaan_type,link|nullable|url',
            'surat_pengalihan_hak_cipta' => 'required|file|mimes:pdf|max:2048',
            'surat_pernyataan_hak_cipta' => 'required|file|mimes:pdf|max:2048',
            'ktp_seluruh_pencipta' => 'required|file|mimes:pdf|max:10240',
            'role' => 'required|in:dosen,mahasiswa',
            'pencipta' => 'required|array',
            'pencipta.*.nama' => 'required|string|max:255',
            'pencipta.*.email' => 'required|email|max:255',
            'pencipta.*.no_hp' => 'nullable|string|max:255',
            'pencipta.*.alamat' => 'nullable|string',
            'tahun_usulan' => 'nullable|string|max:10',
        ], [
            'judul.required' => 'Judul karya wajib diisi.',
            'kategori.required' => 'Kategori wajib diisi.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'nama_pengusul.required' => 'Nama pengusul wajib diisi.',
            'nip_nidn.required_if' => 'NIP/NIDN wajib diisi untuk dosen.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'id_sinta.required' => 'ID SINTA wajib diisi.',
            'jumlah_pencipta.required' => 'Jumlah pencipta wajib diisi.',
            'identitas_ciptaan.required' => 'Jenis ciptaan wajib diisi.',
            'sub_jenis_ciptaan.required' => 'Sub jenis ciptaan wajib diisi.',
            'tanggal_pertama_kali_diumumkan.required' => 'Tanggal pertama kali diumumkan wajib diisi.',
            'contoh_ciptaan_type.required' => 'Pilih tipe contoh ciptaan (upload atau link).',
            'contoh_ciptaan.required_if' => 'File contoh ciptaan wajib diupload.',
            'contoh_ciptaan_link.required_if' => 'Link contoh ciptaan wajib diisi.',
            'surat_pengalihan_hak_cipta.required' => 'Surat pengalihan hak cipta wajib diupload.',
            'surat_pernyataan_hak_cipta.required' => 'Surat pernyataan hak cipta wajib diupload.',
            'ktp_seluruh_pencipta.required' => 'KTP seluruh pencipta wajib diupload.',
            'role.required' => 'Role wajib dipilih.',
            'pencipta.required' => 'Data pencipta wajib diisi minimal satu.',
            'pencipta.*.nama.required' => 'Nama setiap pencipta wajib diisi.',
            'pencipta.*.email.required' => 'Email setiap pencipta wajib diisi.',
        ]);

        // Proses file atau link contoh ciptaan
        $fileKarya = null;
        if ($request->input('contoh_ciptaan_type') === 'upload' && $request->hasFile('contoh_ciptaan')) {
            $fileKarya = $request->file('contoh_ciptaan')->store('dokumen_ciptaan', 'public');
        } elseif ($request->input('contoh_ciptaan_type') === 'link') {
            $fileKarya = $request->input('contoh_ciptaan_link');
        }

        $pathSuratPengalihan = $request->file('surat_pengalihan_hak_cipta')->store('dokumen_pengalihan', 'public');
        $pathSuratPernyataan = $request->file('surat_pernyataan_hak_cipta')->store('dokumen_pernyataan', 'public');
        $pathKtp = $request->file('ktp_seluruh_pencipta')->store('dokumen_ktp', 'public');

        $tahun = date('Y');
        $bulan = date('m');
        $prefix = sprintf('HKI/%s/%s/', $tahun, $bulan);
        $lastPengajuan = PengajuanHki::where('nomor_pengajuan', 'like', $prefix . '%')
            ->orderBy('nomor_pengajuan', 'desc')
            ->first();
        $nomorUrut = $lastPengajuan ? intval(substr($lastPengajuan->nomor_pengajuan, -4)) + 1 : 1;
        do {
            $nomorPengajuan = sprintf('HKI/%s/%s/%04d', $tahun, $bulan, $nomorUrut);
            $exists = PengajuanHki::where('nomor_pengajuan', $nomorPengajuan)->exists();
            if ($exists) $nomorUrut++;
        } while ($exists);

        $pengajuan = PengajuanHki::create([
            'user_id' => Auth::id(),
            'judul_karya' => $validated['judul'] ?? '',
            'kategori' => $validated['kategori'] ?? '',
            'deskripsi' => $validated['deskripsi'] ?? '',
            'nama_pengusul' => $validated['nama_pengusul'] ?? '',
            'nip_nidn' => $validated['nip_nidn'] ?? '',
            'no_hp' => $validated['no_hp'] ?? '',
            'id_sinta' => $validated['id_sinta'] ?? '',
            'jumlah_pencipta' => $validated['jumlah_pencipta'] ?? '',
            'identitas_ciptaan' => $validated['identitas_ciptaan'] ?? '',
            'sub_jenis_ciptaan' => $validated['sub_jenis_ciptaan'] ?? '',
            'tanggal_pertama_kali_diumumkan' => $validated['tanggal_pertama_kali_diumumkan'] ?? '',
            'status' => 'menunggu_validasi',
            'tanggal_pengajuan' => Carbon::now('Asia/Makassar'),
            'nomor_pengajuan' => $nomorPengajuan,
            'role' => $validated['role'] ?? '',
            'file_karya' => $fileKarya,
            'file_dokumen_pendukung' => json_encode([
                'surat_pengalihan' => $pathSuratPengalihan,
                'surat_pernyataan' => $pathSuratPernyataan,
                'ktp' => $pathKtp
            ]),
            'tahun_usulan' => $validated['tahun_usulan'] ?? '',
        ]);

        // Simpan data pencipta dinamis
        if (isset($validated['pencipta'])) {
            foreach ($validated['pencipta'] as $dataPencipta) {
                if (!empty($dataPencipta['email']) && !empty($dataPencipta['nama'])) {
                    $pengajuan->pengaju()->create([
                        'nama' => $dataPencipta['nama'],
                        'email' => $dataPencipta['email'],
                        'no_hp' => $dataPencipta['no_hp'] ?? null,
                        'alamat' => $dataPencipta['alamat'] ?? null,
                        'kecamatan' => $dataPencipta['kecamatan'] ?? null,
                        'kodepos' => $dataPencipta['kodepos'] ?? null,
                    ]);
                }
            }
        }

        Notifikasi::create([
            'user_id' => Auth::id(),
            'pengajuan_hki_id' => $pengajuan->id,
            'judul' => 'Pengajuan HKI Baru',
            'pesan' => 'Pengajuan HKI Anda dengan nomor "' . $nomorPengajuan . '" telah berhasil dibuat dan sedang menunggu validasi.',
            'status' => 'unread',
            'dibaca' => false
        ]);

        return Redirect::to(route('dashboard'))
            ->with('success', 'Pengajuan berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(PengajuanHki $pengajuan): View|RedirectResponse
    {
        if (auth()->user()->role !== 'admin' && $pengajuan->user_id !== Auth::id()) {
            return Redirect::to(route('pengajuan.index'))
                ->with('error', 'Anda tidak memiliki akses ke pengajuan ini');
        }
        return view('pengajuan.show', compact('pengajuan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PengajuanHki $pengajuan): View|RedirectResponse
    {
        if (auth()->user()->role !== 'admin' && $pengajuan->user_id !== Auth::id()) {
            return Redirect::to(route('pengajuan.index'))
                ->with('error', 'Anda tidak memiliki akses ke pengajuan ini');
        }
        if ($pengajuan->status !== 'menunggu_validasi') {
            return Redirect::to(route('pengajuan.index'))
                ->with('error', 'Pengajuan tidak dapat diubah karena sudah diproses');
        }
        return view('pengajuan.edit', compact('pengajuan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PengajuanHki $pengajuan): RedirectResponse
    {
        if ($pengajuan->user_id !== Auth::id()) {
            return Redirect::to(route('pengajuan.index'))
                ->with('error', 'Anda tidak memiliki akses ke pengajuan ini');
        }

        if ($pengajuan->status !== 'menunggu_validasi') {
            return Redirect::to(route('pengajuan.index'))
                ->with('error', 'Pengajuan tidak dapat diubah karena sudah diproses');
        }

        if ($request->has('simpan_draft')) {
            $validated = $request->validate([
                'judul' => 'nullable|string|max:255',
                'kategori' => 'nullable|string|max:255',
                'deskripsi' => 'nullable|string',
                'identitas_ciptaan' => 'nullable|string|in:karya tulis,karya audio visual,karya lainnya',
                'sub_jenis_ciptaan' => 'nullable|string',
                'tahun_usulan' => 'nullable|string|max:10',
                'jumlah_pencipta' => 'nullable|string|in:1 orang,2 orang,3 orang,4 orang,5 orang',
                'pencipta' => 'nullable|array',
                'pencipta.*.nama' => 'nullable|string|max:255',
                'pencipta.*.email' => 'nullable|email|max:255',
                'pencipta.*.no_hp' => 'nullable|string|max:255',
                'pencipta.*.alamat' => 'nullable|string',
                'pencipta.*.kecamatan' => 'nullable|string|max:255',
                'pencipta.*.kodepos' => 'nullable|string|max:20',
            ], [
                'nama_pengusul.required' => 'Nama pengusul wajib diisi.',
                'no_hp.required' => 'Nomor HP wajib diisi.',
                'id_sinta.required' => 'ID SINTA wajib diisi.',
                'jumlah_pencipta.required' => 'Jumlah pencipta wajib diisi.',
                'identitas_ciptaan.required' => 'Jenis ciptaan wajib diisi.',
                'sub_jenis_ciptaan.required' => 'Sub jenis ciptaan wajib diisi.',
                'tanggal_pertama_kali_diumumkan.required' => 'Tanggal pertama kali diumumkan wajib diisi.',
                'role.required' => 'Role wajib dipilih.',
            ]);
            // Validasi: jika ada field pencipta yang diisi sebagian, tampilkan notifikasi error
            if (isset($validated['pencipta'])) {
                foreach ($validated['pencipta'] as $idx => $dataPencipta) {
                    $filled = false;
                    foreach (['nama','email','no_hp','alamat','kecamatan','kodepos'] as $f) {
                        if (!empty($dataPencipta[$f])) $filled = true;
                    }
                    if ($filled && (empty($dataPencipta['nama']) || empty($dataPencipta['email']))) {
                        return redirect()->back()->withInput()->with('error', 'Nama dan Email wajib diisi untuk setiap pencipta jika ingin menyimpan draft.');
                    }
                }
            }
            $pengajuan->update([
                'judul_karya' => $validated['judul'] ?? null,
                'kategori' => $validated['kategori'] ?? null,
                'deskripsi' => $validated['deskripsi'] ?? null,
                'identitas_ciptaan' => $validated['identitas_ciptaan'] ?? null,
                'sub_jenis_ciptaan' => $validated['sub_jenis_ciptaan'] ?? null,
                'tahun_usulan' => $validated['tahun_usulan'] ?? null,
                'jumlah_pencipta' => $validated['jumlah_pencipta'] ?? null,
                'status' => 'draft',
            ]);
            $pengajuan->pengaju()->delete();
            if (isset($validated['pencipta'])) {
                foreach ($validated['pencipta'] as $dataPencipta) {
                    if (!empty($dataPencipta['nama']) && !empty($dataPencipta['email'])) {
                        $pengajuan->pengaju()->create([
                            'nama' => $dataPencipta['nama'] ?? null,
                            'email' => $dataPencipta['email'] ?? null,
                            'no_hp' => $dataPencipta['no_hp'] ?? null,
                            'alamat' => $dataPencipta['alamat'] ?? null,
                            'kecamatan' => $dataPencipta['kecamatan'] ?? null,
                            'kodepos' => $dataPencipta['kodepos'] ?? null,
                        ]);
                    }
                }
            }
            return Redirect::to(route('draft.edit', $pengajuan->id))->with('success', 'Draft berhasil disimpan.');
        }

        // Validasi normal untuk simpan final
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'identitas_ciptaan' => 'required|string|in:karya tulis,karya audio visual,karya lainnya',
            'sub_jenis_ciptaan' => 'required|string|in:Buku,E-Book,Diktat,Modul,Buku Panduan/Petunjuk,Karya Ilmiah,Karya Tulis/Artikel,Laporan Penelitian,Jurnal,Kuliah,Karya Rekaman Video,Karya Siaran Video,Program Komputer,Permainan Video,Basis Data',
            'tahun_usulan' => 'nullable|string|max:10',
            'jumlah_pencipta' => 'required|string|in:1 orang,2 orang,3 orang,4 orang,5 orang',
            'id_sinta' => 'required|string|max:255',
            'pencipta' => 'required|array',
            'pencipta.*.nama' => 'required|string|max:255',
            'pencipta.*.email' => 'required|email|max:255',
            'pencipta.*.no_hp' => 'nullable|string|max:255',
            'pencipta.*.alamat' => 'nullable|string',
            'pencipta.*.kecamatan' => 'nullable|string|max:255',
            'pencipta.*.kodepos' => 'nullable|string|max:20',
        ]);

        $pengajuan->update([
            'judul_karya' => $validated['judul'],
            'kategori' => $validated['kategori'],
            'deskripsi' => $validated['deskripsi'],
            'identitas_ciptaan' => $validated['identitas_ciptaan'],
            'sub_jenis_ciptaan' => $validated['sub_jenis_ciptaan'],
            'tahun_usulan' => $validated['tahun_usulan'] ?? null,
            'jumlah_pencipta' => $validated['jumlah_pencipta'],
            'status' => 'menunggu_validasi',
        ]);

        // Update data pencipta
        $pengajuan->pengaju()->delete();
        foreach ($validated['pencipta'] as $dataPencipta) {
            if (!empty($dataPencipta['email']) && !empty($dataPencipta['nama'])) {
                $pengajuan->pengaju()->create([
                    'nama' => $dataPencipta['nama'],
                    'email' => $dataPencipta['email'],
                    'no_hp' => $dataPencipta['no_hp'] ?? null,
                    'alamat' => $dataPencipta['alamat'] ?? null,
                    'kecamatan' => $dataPencipta['kecamatan'] ?? null,
                    'kodepos' => $dataPencipta['kodepos'] ?? null,
                ]);
            }
        }

        return Redirect::to(route('pengajuan.index'))
            ->with('success', 'Pengajuan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PengajuanHki $pengajuan): RedirectResponse
    {
        if (auth()->user()->role !== 'admin' && $pengajuan->user_id !== Auth::id()) {
            return Redirect::to(route('pengajuan.index'))
                ->with('error', 'Anda tidak memiliki akses ke pengajuan ini');
        }
        if ($pengajuan->status !== 'menunggu_validasi') {
            return Redirect::to(route('pengajuan.index'))
                ->with('error', 'Pengajuan tidak dapat dihapus karena sudah diproses');
        }
        Storage::delete($pengajuan->file_karya);
        $pengajuan->delete();
        return Redirect::to(route('pengajuan.index'))
            ->with('success', 'Pengajuan berhasil dihapus');
    }

    /**
     * Tampilkan daftar draft milik user.
     */
    public function draftIndex(): View
    {
        $drafts = PengajuanHki::where('user_id', Auth::id())
            ->where('status', 'draft')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('pengajuan.draft', compact('drafts'));
    }

    /**
     * Simpan pengajuan sebagai draft.
     */
    public function storeDraft(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);
        PengajuanHki::create([
            'user_id' => Auth::id(),
            'judul_karya' => $validated['judul'] ?? $request->input('judul', ''),
            'kategori' => $validated['kategori'] ?? $request->input('kategori', ''),
            'deskripsi' => $validated['deskripsi'] ?? $request->input('deskripsi', ''),
            'nama_pengusul' => $request->input('nama_pengusul', ''),
            'nip_nidn' => $request->input('nip_nidn', ''),
            'no_hp' => $request->input('no_hp', ''),
            'id_sinta' => $request->input('id_sinta', ''),
            'jumlah_pencipta' => $request->input('jumlah_pencipta', ''),
            'identitas_ciptaan' => $request->input('identitas_ciptaan', ''),
            'sub_jenis_ciptaan' => $request->input('sub_jenis_ciptaan', ''),
            'tanggal_pertama_kali_diumumkan' => $request->input('tanggal_pertama_kali_diumumkan', ''),
            'tahun_usulan' => $request->input('tahun_usulan', ''),
            'role' => $request->input('role', $request->old('role', '')),
            'file_karya' => null,
            'file_dokumen_pendukung' => null,
            'status' => 'draft',
            'catatan_validasi' => null,
            'catatan_persetujuan' => null,
            'nomor_pengajuan' => null,
            'tanggal_pengajuan' => \Carbon\Carbon::now('Asia/Makassar'),
        ]);
        return Redirect::to(route('draft.index'))->with('success', 'Draft berhasil disimpan');
    }

    /**
     * Edit draft.
     */
    public function editDraft(PengajuanHki $pengajuan): View|RedirectResponse
    {
        if ($pengajuan->user_id !== Auth::id() || $pengajuan->status !== 'draft') {
            return Redirect::to(route('draft.index'))->with('error', 'Akses tidak valid');
        }
        return view('pengajuan.edit_draft', compact('pengajuan'));
    }

    /**
     * Hapus draft.
     */
    public function destroyDraft(PengajuanHki $pengajuan): RedirectResponse
    {
        if ($pengajuan->user_id !== Auth::id() || $pengajuan->status !== 'draft') {
            return Redirect::to(route('draft.index'))->with('error', 'Akses tidak valid');
        }
        $pengajuan->delete();
        return Redirect::to(route('draft.index'))->with('success', 'Draft berhasil dihapus');
    }

    /**
     * Submit draft menjadi pengajuan.
     */
    public function submitDraft(PengajuanHki $pengajuan): RedirectResponse
    {
        if ($pengajuan->user_id !== Auth::id() || $pengajuan->status !== 'draft') {
            return Redirect::to(route('draft.index'))->with('error', 'Akses tidak valid');
        }
        $pengajuan->update(['status' => 'menunggu_validasi']);
        return Redirect::to(route('pengajuan.index'))->with('success', 'Draft berhasil diajukan');
    }

    /**
     * Update draft ciptaan.
     */
    public function updateDraft(Request $request, PengajuanHki $pengajuan): RedirectResponse
    {
        if ($pengajuan->user_id !== Auth::id() || $pengajuan->status !== 'draft') {
            return Redirect::to(route('draft.index'))->with('error', 'Akses tidak valid');
        }
        // Jika tombol Simpan sebagai Draft ditekan
        if ($request->has('save_as_draft')) {
            // Update field utama
            $pengajuan->update([
                'judul_karya' => $request->input('judul', $pengajuan->judul_karya),
                'kategori' => $request->input('kategori', $pengajuan->kategori),
                'deskripsi' => $request->input('deskripsi', $pengajuan->deskripsi),
                'nama_pengusul' => $request->input('nama_pengusul', $pengajuan->nama_pengusul),
                'nip_nidn' => $request->input('nip_nidn', $pengajuan->nip_nidn),
                'no_hp' => $request->input('no_hp', $pengajuan->no_hp),
                'id_sinta' => $request->input('id_sinta', $pengajuan->id_sinta),
                'jumlah_pencipta' => $request->input('jumlah_pencipta', $pengajuan->jumlah_pencipta),
                'identitas_ciptaan' => $request->input('identitas_ciptaan', $pengajuan->identitas_ciptaan),
                'sub_jenis_ciptaan' => $request->input('sub_jenis_ciptaan', $pengajuan->sub_jenis_ciptaan),
                'tanggal_pertama_kali_diumumkan' => $request->input('tanggal_pertama_kali_diumumkan', $pengajuan->tanggal_pertama_kali_diumumkan),
                'tahun_usulan' => $request->input('tahun_usulan', $pengajuan->tahun_usulan),
                'role' => $request->input('role', $pengajuan->role),
            ]);
            // Update dokumen jika ada file baru
            $dokumen = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];
            if ($request->hasFile('contoh_ciptaan')) {
                $pengajuan->file_karya = $request->file('contoh_ciptaan')->store('dokumen_ciptaan', 'public');
            }
            if ($request->hasFile('surat_pengalihan_hak_cipta')) {
                $dokumen['surat_pengalihan'] = $request->file('surat_pengalihan_hak_cipta')->store('dokumen_pengalihan', 'public');
            }
            if ($request->hasFile('surat_pernyataan_hak_cipta')) {
                $dokumen['surat_pernyataan'] = $request->file('surat_pernyataan_hak_cipta')->store('dokumen_pernyataan', 'public');
            }
            if ($request->hasFile('ktp_seluruh_pencipta')) {
                $dokumen['ktp'] = $request->file('ktp_seluruh_pencipta')->store('dokumen_ktp', 'public');
            }
            $pengajuan->file_dokumen_pendukung = json_encode($dokumen);
            $pengajuan->save();
            // Data pencipta dinamis (opsional, jika ada perubahan)
            if ($request->has('pencipta')) {
                $pengajuan->pengaju()->delete();
                foreach ($request->input('pencipta') as $dataPencipta) {
                    if (!empty($dataPencipta['email']) && !empty($dataPencipta['nama'])) {
                        $pengajuan->pengaju()->create([
                            'nama' => $dataPencipta['nama'],
                            'email' => $dataPencipta['email'],
                            'no_hp' => $dataPencipta['no_hp'] ?? null,
                            'alamat' => $dataPencipta['alamat'] ?? null,
                            'kecamatan' => $dataPencipta['kecamatan'] ?? null,
                            'kodepos' => $dataPencipta['kodepos'] ?? null,
                        ]);
                    }
                }
            }
            return Redirect::to(route('draft.index'))->with('success', 'Draft berhasil diperbarui');
        }
        $validated = $request->validate([
            'judul' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'nama_pengusul' => 'required|string|max:255',
            'nip_nidn' => 'nullable|string|max:255',
            'no_hp' => 'required|string|max:255',
            'id_sinta' => 'required|string|max:255',
            'jumlah_pencipta' => 'required|string|in:1 orang,2 orang,3 orang,4 orang,5 orang',
            'identitas_ciptaan' => 'required|string|in:karya tulis,karya audio visual,karya lainnya',
            'sub_jenis_ciptaan' => 'required|string',
            'tanggal_pertama_kali_diumumkan' => 'required|date',
            'tahun_usulan' => 'nullable|string|max:10',
            'role' => 'required|in:dosen,mahasiswa',
            // dokumen dan pencipta dinamis tidak required saat draft
        ], [
            'nama_pengusul.required' => 'Nama pengusul wajib diisi.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'id_sinta.required' => 'ID SINTA wajib diisi.',
            'jumlah_pencipta.required' => 'Jumlah pencipta wajib diisi.',
            'identitas_ciptaan.required' => 'Jenis ciptaan wajib diisi.',
            'sub_jenis_ciptaan.required' => 'Sub jenis ciptaan wajib diisi.',
            'tanggal_pertama_kali_diumumkan.required' => 'Tanggal pertama kali diumumkan wajib diisi.',
            'role.required' => 'Role wajib dipilih.',
        ]);
        $pengajuan->update([
            'judul_karya' => $validated['judul'] ?? null,
            'kategori' => $validated['kategori'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'nama_pengusul' => $validated['nama_pengusul'] ?? $request->input('nama_pengusul'),
            'nip_nidn' => $validated['nip_nidn'] ?? $request->input('nip_nidn'),
            'no_hp' => $validated['no_hp'] ?? $request->input('no_hp'),
            'id_sinta' => $validated['id_sinta'] ?? $request->input('id_sinta'),
            'jumlah_pencipta' => $validated['jumlah_pencipta'] ?? $request->input('jumlah_pencipta'),
            'identitas_ciptaan' => $validated['identitas_ciptaan'] ?? $request->input('identitas_ciptaan'),
            'sub_jenis_ciptaan' => $validated['sub_jenis_ciptaan'] ?? $request->input('sub_jenis_ciptaan'),
            'tanggal_pertama_kali_diumumkan' => $validated['tanggal_pertama_kali_diumumkan'] ?? $request->input('tanggal_pertama_kali_diumumkan'),
            'tahun_usulan' => $validated['tahun_usulan'] ?? $request->input('tahun_usulan'),
            'role' => $validated['role'] ?? $request->input('role', $pengajuan->role),
        ]);
        // Update data pencipta dinamis
        if ($request->has('pencipta')) {
            $pengajuan->pengaju()->delete();
            foreach ($request->input('pencipta') as $dataPencipta) {
                if (!empty($dataPencipta['email']) && !empty($dataPencipta['nama'])) {
                    $pengajuan->pengaju()->create([
                        'nama' => $dataPencipta['nama'],
                        'email' => $dataPencipta['email'],
                        'no_hp' => $dataPencipta['no_hp'] ?? null,
                        'alamat' => $dataPencipta['alamat'] ?? null,
                        'kecamatan' => $dataPencipta['kecamatan'] ?? null,
                        'kodepos' => $dataPencipta['kodepos'] ?? null,
                    ]);
                }
            }
        }
        // Update dokumen jika ada file baru
        $dokumen = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];
        if ($request->hasFile('contoh_ciptaan')) {
            $pengajuan->file_karya = $request->file('contoh_ciptaan')->store('dokumen_ciptaan', 'public');
        }
        if ($request->hasFile('surat_pengalihan_hak_cipta')) {
            $dokumen['surat_pengalihan'] = $request->file('surat_pengalihan_hak_cipta')->store('dokumen_pengalihan', 'public');
        }
        if ($request->hasFile('surat_pernyataan_hak_cipta')) {
            $dokumen['surat_pernyataan'] = $request->file('surat_pernyataan_hak_cipta')->store('dokumen_pernyataan', 'public');
        }
        if ($request->hasFile('ktp_seluruh_pencipta')) {
            $dokumen['ktp'] = $request->file('ktp_seluruh_pencipta')->store('dokumen_ktp', 'public');
        }
        $pengajuan->file_dokumen_pendukung = json_encode($dokumen);
        $pengajuan->save();
        // Jika tombol Kirim ditekan
        if ($request->has('ajukan')) {
            // Validasi dokumen wajib
            $dokumen = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];
            $error = null;
            if (empty($pengajuan->file_karya)) {
                $error = 'Contoh ciptaan wajib diupload atau diisi link.';
            } elseif (empty($dokumen['surat_pengalihan'])) {
                $error = 'Surat pengalihan hak cipta wajib diupload.';
            } elseif (empty($dokumen['surat_pernyataan'])) {
                $error = 'Surat pernyataan hak cipta wajib diupload.';
            } elseif (empty($dokumen['ktp'])) {
                $error = 'KTP seluruh pencipta wajib diupload.';
            }
            if ($error) {
                return Redirect::back()->withInput()->with('error', $error);
            }
            $pengajuan->update(['status' => 'menunggu_validasi']);
            return Redirect::to(route('draft.index'))->with('success', 'Draft berhasil dikirim dan menunggu validasi');
        }
        return Redirect::to(route('draft.index'))->with('success', 'Draft berhasil diperbarui');
    }

    /**
     * Hapus file dokumen pada draft.
     */
    public function deleteDraftFile(Request $request, PengajuanHki $pengajuan)
    {
        if ($pengajuan->user_id !== Auth::id() || $pengajuan->status !== 'draft') {
            return Redirect::to(route('draft.edit', $pengajuan->id))->with('error', 'Akses tidak valid');
        }
        $request->validate([
            'tipe' => 'required|in:contoh_ciptaan,surat_pengalihan,surat_pernyataan,ktp'
        ]);
        $tipe = $request->input('tipe');
        $dokumen = is_string($pengajuan->file_dokumen_pendukung) ? json_decode($pengajuan->file_dokumen_pendukung, true) : ($pengajuan->file_dokumen_pendukung ?? []);
        $success = false;
        if ($tipe === 'contoh_ciptaan' && $pengajuan->file_karya) {
            Storage::disk('public')->delete($pengajuan->file_karya);
            $pengajuan->file_karya = null;
            $success = true;
        } elseif ($tipe === 'surat_pengalihan' && isset($dokumen['surat_pengalihan'])) {
            Storage::disk('public')->delete($dokumen['surat_pengalihan']);
            $dokumen['surat_pengalihan'] = null;
            $success = true;
        } elseif ($tipe === 'surat_pernyataan' && isset($dokumen['surat_pernyataan'])) {
            Storage::disk('public')->delete($dokumen['surat_pernyataan']);
            $dokumen['surat_pernyataan'] = null;
            $success = true;
        } elseif ($tipe === 'ktp' && isset($dokumen['ktp'])) {
            Storage::disk('public')->delete($dokumen['ktp']);
            $dokumen['ktp'] = null;
            $success = true;
        }
        $pengajuan->file_dokumen_pendukung = json_encode($dokumen);
        $pengajuan->save();
        if ($tipe === 'contoh_ciptaan') {
            $pengajuan->save();
        }
        return Redirect::to(route('draft.edit', $pengajuan->id))
            ->with($success ? 'success' : 'error', $success ? 'File berhasil dihapus.' : 'File tidak ditemukan.');
    }
} 