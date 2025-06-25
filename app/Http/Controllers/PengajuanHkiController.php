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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PengajuanHkiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        // Admin diarahkan ke dashboard khusus
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $query = PengajuanHki::query();

        // Hanya data milik user login (dosen / mahasiswa / direktur jika ingin)
        $query->where('user_id', Auth::id());

        // Kecualikan draft
        $query->where('status', '!=', 'draft');

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

        // Rentang tanggal
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
            
        // Log untuk debugging
        Log::info('Data pengajuan di index (with filter)', [
            'user_id' => Auth::id(),
            'filters' => $request->all(),
            'total_data' => $pengajuan->total(),
            'data_ids' => collect($pengajuan->items())->pluck('id')->toArray(),
            'data_status' => collect($pengajuan->items())->pluck('status')->toArray()
        ]);
        
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
        try {
        // Logging awal untuk debug semua request
        Log::info('STORE METHOD CALLED', [
            'user_id' => Auth::id(),
            'has_save_as_draft' => $request->has('save_as_draft'),
            'has_submit_final' => $request->has('submit_final'),
            'save_as_draft_value' => $request->input('save_as_draft'),
            'submit_final_value' => $request->input('submit_final'),
            'all_parameters' => array_keys($request->all())
        ]);
        
        // Jika tombol simpan draft ditekan
        if ($request->has('save_as_draft')) {
            // Logging untuk debug
            Log::info('ENTERING DRAFT LOGIC', [
                'user_id' => Auth::id(),
                'has_save_as_draft' => $request->has('save_as_draft'),
                'save_as_draft_value' => $request->input('save_as_draft'),
                'request_data' => $request->all()
            ]);
            
            $validated = $request->validate([
                'judul' => 'nullable|string|max:255',
                'deskripsi' => 'nullable|string',
                'nama_pengusul' => 'nullable|string|max:255',
                'nip_nidn' => 'nullable|string|max:255',
                'no_hp' => 'nullable|string|max:255',
                'id_sinta' => 'nullable|string|max:255',
                    'jumlah_pencipta' => 'nullable|in:1,2,3,4,5',
                'identitas_ciptaan' => 'nullable|string|in:karya tulis,karya audio visual,karya lainnya',
                'sub_jenis_ciptaan' => 'nullable|string',
                'tanggal_pertama_kali_diumumkan' => 'nullable|date',
                'tahun_usulan' => 'nullable|string|max:10',
                'role' => 'nullable|in:dosen,mahasiswa',
                'pencipta' => 'nullable|array',
                'pencipta.*.nama' => 'nullable|string|max:255',
                'pencipta.*.email' => 'nullable|email|max:255',
                'pencipta.*.no_hp' => 'nullable|string|max:255',
                'pencipta.*.alamat' => 'nullable|string',
                'pencipta.*.kecamatan' => 'nullable|string|max:255',
                'pencipta.*.kodepos' => 'nullable|string|max:20',
                // dokumen dan pencipta dinamis tidak required saat draft
            ]);
            
            $pengajuan = PengajuanHki::create([
                'user_id' => Auth::id(),
                'judul_karya' => $validated['judul'] ?? null,
                'deskripsi' => $validated['deskripsi'] ?? null,
                'file_karya' => null,
                'file_dokumen_pendukung' => json_encode([]),
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
            
            Log::info('Draft created successfully', [
                'draft_id' => $pengajuan->id,
                'user_id' => Auth::id(),
                'status' => $pengajuan->status
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
            // Jika tombol Kirim (submit_final) ditekan
            if ($request->has('submit_final')) {
                Log::info('ENTERING SUBMIT FINAL LOGIC', [
                    'user_id' => Auth::id(),
                    'has_submit_final' => $request->has('submit_final'),
                    'submit_final_value' => $request->input('submit_final'),
                    'also_has_save_as_draft' => $request->has('save_as_draft')
                ]);
                $validated = $request->validate([
                    'judul' => 'required|string|max:255',
                    'deskripsi' => 'required|string',
                    'nama_pengusul' => 'required|string|max:255',
                    'nip_nidn' => ['nullable', 'string', 'max:255', 'required_if:role,dosen'],
                    'no_hp' => 'required|string|max:255',
                    'id_sinta' => 'required_if:role,dosen|string|max:255',
                    'jumlah_pencipta' => 'required|in:1,2,3,4,5',
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
                    'pencipta.*.kecamatan' => 'nullable|string|max:255',
                    'pencipta.*.kodepos' => 'nullable|string|max:20',
                    'tahun_usulan' => 'nullable|string|max:10',
                ], [
                    'judul.required' => 'Judul karya wajib diisi.',
                    'deskripsi.required' => 'Deskripsi wajib diisi.',
                    'nama_pengusul.required' => 'Nama pengusul wajib diisi.',
                    'nip_nidn.required_if' => 'NIP/NIDN wajib diisi untuk dosen.',
                    'no_hp.required' => 'Nomor HP wajib diisi.',
                    'id_sinta.required_if' => 'ID SINTA wajib diisi untuk dosen.',
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
                // Clear cache jika ada
                if (function_exists('cache')) {
                    cache()->forget('pengajuan_user_' . Auth::id());
                }
                return Redirect::to(route('pengajuan.index'))->with('success', 'Pengajuan berhasil dibuat');
            }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'nama_pengusul' => 'required|string|max:255',
            'nip_nidn' => ['nullable', 'string', 'max:255', 'required_if:role,dosen'],
            'no_hp' => 'required|string|max:255',
            'id_sinta' => 'required_if:role,dosen|string|max:255',
                'jumlah_pencipta' => 'required|in:1,2,3,4,5',
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
            'pencipta.*.kecamatan' => 'nullable|string|max:255',
            'pencipta.*.kodepos' => 'nullable|string|max:20',
            'tahun_usulan' => 'nullable|string|max:10',
        ], [
            'judul.required' => 'Judul karya wajib diisi.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'nama_pengusul.required' => 'Nama pengusul wajib diisi.',
            'nip_nidn.required_if' => 'NIP/NIDN wajib diisi untuk dosen.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
                'id_sinta.required_if' => 'ID SINTA wajib diisi untuk dosen.',
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

        // Log untuk debugging
        Log::info('Pengajuan berhasil dibuat', [
            'id' => $pengajuan->id,
            'user_id' => $pengajuan->user_id,
            'status' => $pengajuan->status,
            'nomor_pengajuan' => $pengajuan->nomor_pengajuan,
            'judul_karya' => $pengajuan->judul_karya
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

        // Clear cache jika ada
        if (function_exists('cache')) {
            cache()->forget('pengajuan_user_' . Auth::id());
        }
        
        return Redirect::to(route('pengajuan.index'))->with('success', 'Pengajuan berhasil dibuat');
        } catch (ValidationException $e) {
            Log::error('Validation error in store', [
                'errors' => $e->errors(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            return Redirect::back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error in store method', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return Redirect::back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
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
                'deskripsi' => 'nullable|string',
                'identitas_ciptaan' => 'nullable|string|in:karya tulis,karya audio visual,karya lainnya',
                'sub_jenis_ciptaan' => 'nullable|string',
                'tahun_usulan' => 'nullable|string|max:10',
                'jumlah_pencipta' => 'nullable|in:1,2,3,4,5',
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
                'id_sinta.required_if' => 'ID SINTA wajib diisi untuk dosen.',
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
            'deskripsi' => 'required|string',
            'identitas_ciptaan' => 'required|string|in:karya tulis,karya audio visual,karya lainnya',
            'sub_jenis_ciptaan' => 'required|string|in:Buku,E-Book,Diktat,Modul,Buku Panduan/Petunjuk,Karya Ilmiah,Karya Tulis/Artikel,Laporan Penelitian,Jurnal,Kuliah,Karya Rekaman Video,Karya Siaran Video,Program Komputer,Permainan Video,Basis Data',
            'tahun_usulan' => 'nullable|string|max:10',
            'jumlah_pencipta' => 'required|in:1,2,3,4,5',
            'id_sinta' => 'required_if:role,dosen|string|max:255',
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

        return Redirect::to(route('pengajuan.index'))->with('success', 'Pengajuan berhasil diperbarui');
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
            
        Log::info('Draft index loaded', [
            'user_id' => Auth::id(),
            'drafts_count' => $drafts->count(),
            'draft_ids' => $drafts->pluck('id')->toArray(),
            'draft_titles' => $drafts->pluck('judul_karya')->toArray()
        ]);
        
        return view('pengajuan.draft', compact('drafts'));
    }

    /**
     * Simpan pengajuan sebagai draft.
     */
    public function storeDraft(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);
        PengajuanHki::create([
            'user_id' => Auth::id(),
            'judul_karya' => $validated['judul'] ?? $request->input('judul', ''),
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
            'file_dokumen_pendukung' => json_encode([]),
            'status' => 'draft',
            'catatan_validasi' => null,
            'catatan_persetujuan' => null,
            'nomor_pengajuan' => null,
            'tanggal_pengajuan' => \Carbon\Carbon::now('Asia/Makassar'),
        ]);
        return Redirect::to(route('draft.index'))->with('success', 'Draft berhasil disimpan');
    }

    /**1
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
        
        // Generate nomor pengajuan jika belum ada
        if (empty($pengajuan->nomor_pengajuan)) {
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
            
            $pengajuan->update([
                'status' => 'menunggu_validasi',
                'nomor_pengajuan' => $nomorPengajuan,
                'tanggal_pengajuan' => Carbon::now('Asia/Makassar')
            ]);
        } else {
            $pengajuan->update(['status' => 'menunggu_validasi']);
        }
        
        // Buat notifikasi
        Notifikasi::create([
            'user_id' => Auth::id(),
            'pengajuan_hki_id' => $pengajuan->id,
            'judul' => 'Pengajuan HKI Baru',
            'pesan' => 'Pengajuan HKI Anda dengan nomor "' . $pengajuan->nomor_pengajuan . '" telah berhasil dibuat dan sedang menunggu validasi.',
            'status' => 'unread',
            'dibaca' => false
        ]);
        
        // Clear cache jika ada
        if (function_exists('cache')) {
            cache()->forget('pengajuan_user_' . Auth::id());
        }
        
        return Redirect::to(route('pengajuan.index'))->with('success', 'Draft berhasil diajukan');
    }

    /**
     * Update draft ciptaan.
     */
    public function updateDraft(Request $request, PengajuanHki $pengajuan): RedirectResponse
    {
        // Cek authentication terlebih dahulu
        if (!auth()->check()) {
            Log::warning('updateDraft: User not authenticated');
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        // Logging detail untuk debugging
        Log::info('updateDraft called', [
            'pengajuan_id' => $pengajuan->id,
            'user_id' => auth()->id(),
            'user_authenticated' => auth()->check(),
            'session_id' => session()->getId(),
            'csrf_token' => substr($request->input('_token'), 0, 10) . '...',
            'request_method' => $request->method(),
            'has_ajukan' => $request->has('ajukan'),
            'has_save_as_draft' => $request->has('save_as_draft'),
            'ajukan_value' => $request->input('ajukan'),
            'save_as_draft_value' => $request->input('save_as_draft'),
            'action_type' => $request->has('ajukan') ? 'submit' : 'save_draft',
            'request_keys_count' => count($request->all())
        ]);
        
        if ($pengajuan->user_id !== Auth::id() || $pengajuan->status !== 'draft') {
            Log::warning('updateDraft access denied', [
                'pengajuan_user_id' => $pengajuan->user_id,
                'current_user_id' => Auth::id(),
                'pengajuan_status' => $pengajuan->status
            ]);
            return Redirect::to(route('draft.index'))->with('error', 'Akses tidak valid');
        }
        
        // Jika tombol Simpan sebagai Draft ditekan
        if ($request->has('save_as_draft')) {
            // Update field utama
            $pengajuan->update([
                'judul_karya' => $request->input('judul', $pengajuan->judul_karya),
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
                'role' => in_array($request->input('role'), ['dosen','mahasiswa']) ? $request->input('role') : $pengajuan->role,
            ]);
            
            // Update dokumen jika ada file baru
            $dokumen = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];
            if ($request->hasFile('contoh_ciptaan')) {
                $pengajuan->file_karya = $request->file('contoh_ciptaan')->store('dokumen_ciptaan', 'public');
                $pengajuan->save();
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
        
        // Jika tombol Kirim ditekan - implementasi lengkap seperti di create
        if ($request->has('ajukan')) {
            Log::info('Processing ajukan submission', [
                'pengajuan_id' => $pengajuan->id,
                'existing_file_karya' => $pengajuan->file_karya,
                'existing_dokumen' => $pengajuan->file_dokumen_pendukung
            ]);
            
            // Validasi lengkap seperti di create method
            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'nama_pengusul' => 'required|string|max:255',
                'nip_nidn' => ['nullable', 'string', 'max:255', 'required_if:role,dosen'],
                'no_hp' => 'required|string|max:255',
                'id_sinta' => 'required_if:role,dosen|string|max:255',
                'jumlah_pencipta' => 'required|in:1 orang,2 orang,3 orang,4 orang,5 orang',
                'identitas_ciptaan' => 'required|string|in:karya tulis,karya audio visual,karya lainnya',
                'sub_jenis_ciptaan' => 'required|string|in:Buku,E-Book,Diktat,Modul,Buku Panduan/Petunjuk,Karya Ilmiah,Karya Tulis/Artikel,Laporan Penelitian,Jurnal,Kuliah,Karya Rekaman Video,Karya Siaran Video,Program Komputer,Permainan Video,Basis Data',
                'tanggal_pertama_kali_diumumkan' => 'required|date',
                'contoh_ciptaan_type' => 'required|in:upload,link',
                'contoh_ciptaan' => 'nullable|file|mimes:pdf,mp4,mp3,jpg,jpeg,png,gif,svg,webp,doc,docx|max:10240',
                'contoh_ciptaan_link' => 'nullable|url',
                'surat_pengalihan_hak_cipta' => 'nullable|file|mimes:pdf|max:2048',
                'surat_pernyataan_hak_cipta' => 'nullable|file|mimes:pdf|max:2048',
                'ktp_seluruh_pencipta' => 'nullable|file|mimes:pdf|max:10240',
                'role' => 'required|in:dosen,mahasiswa',
                'pencipta' => 'required|array',
                'pencipta.*.nama' => 'required|string|max:255',
                'pencipta.*.email' => 'required|email|max:255',
                'pencipta.*.no_hp' => 'nullable|string|max:255',
                'pencipta.*.alamat' => 'nullable|string',
                'pencipta.*.kecamatan' => 'nullable|string|max:255',
                'pencipta.*.kodepos' => 'nullable|string|max:20',
                'tahun_usulan' => 'nullable|string|max:10',
            ], [
                'judul.required' => 'Judul karya wajib diisi.',
                'deskripsi.required' => 'Deskripsi wajib diisi.',
                'nama_pengusul.required' => 'Nama pengusul wajib diisi.',
                'nip_nidn.required_if' => 'NIP/NIDN wajib diisi untuk dosen.',
                'no_hp.required' => 'Nomor HP wajib diisi.',
                'id_sinta.required_if' => 'ID SINTA wajib diisi untuk dosen.',
                'jumlah_pencipta.required' => 'Jumlah pencipta wajib diisi.',
                'identitas_ciptaan.required' => 'Jenis ciptaan wajib diisi.',
                'sub_jenis_ciptaan.required' => 'Sub jenis ciptaan wajib diisi.',
                'tanggal_pertama_kali_diumumkan.required' => 'Tanggal pertama kali diumumkan wajib diisi.',
                'contoh_ciptaan_type.required' => 'Pilih tipe contoh ciptaan (upload atau link).',
                'role.required' => 'Role wajib dipilih.',
                'pencipta.required' => 'Data pencipta wajib diisi minimal satu.',
                'pencipta.*.nama.required' => 'Nama setiap pencipta wajib diisi.',
                'pencipta.*.email.required' => 'Email setiap pencipta wajib diisi.',
            ]);
            
            Log::info('Validation passed', ['validated_data_keys' => array_keys($validated)]);
            
            // Proses file dokumen - ambil yang sudah ada sebagai basis
            $dokumen = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];
            
            // Update file contoh ciptaan
            if ($request->input('contoh_ciptaan_type') === 'upload') {
            if ($request->hasFile('contoh_ciptaan')) {
                    // Hapus file lama jika ada
                    if ($pengajuan->file_karya && !str_starts_with($pengajuan->file_karya, 'http')) {
                        Storage::disk('public')->delete($pengajuan->file_karya);
                    }
                $pengajuan->file_karya = $request->file('contoh_ciptaan')->store('dokumen_ciptaan', 'public');
                    Log::info('New file uploaded for contoh_ciptaan', ['file_path' => $pengajuan->file_karya]);
                }
                // Jika tidak ada file baru dan tidak ada file lama, error
                elseif (empty($pengajuan->file_karya)) {
                    Log::warning('No contoh_ciptaan file provided');
                    return back()->withInput()->with('error', 'File contoh ciptaan wajib diupload.');
                }
            } elseif ($request->input('contoh_ciptaan_type') === 'link') {
                if ($request->filled('contoh_ciptaan_link')) {
                $pengajuan->file_karya = $request->input('contoh_ciptaan_link');
                    Log::info('Link provided for contoh_ciptaan', ['link' => $pengajuan->file_karya]);
                } elseif (empty($pengajuan->file_karya)) {
                    Log::warning('No contoh_ciptaan link provided');
                    return back()->withInput()->with('error', 'Link contoh ciptaan wajib diisi.');
                }
            }
            
            // Update dokumen pendukung - gunakan file baru jika ada, atau pertahankan yang lama
            if ($request->hasFile('surat_pengalihan_hak_cipta')) {
                if (isset($dokumen['surat_pengalihan'])) {
                    Storage::disk('public')->delete($dokumen['surat_pengalihan']);
                }
                $dokumen['surat_pengalihan'] = $request->file('surat_pengalihan_hak_cipta')->store('dokumen_pengalihan', 'public');
                Log::info('New surat_pengalihan uploaded', ['file_path' => $dokumen['surat_pengalihan']]);
            }
            
            if ($request->hasFile('surat_pernyataan_hak_cipta')) {
                if (isset($dokumen['surat_pernyataan'])) {
                    Storage::disk('public')->delete($dokumen['surat_pernyataan']);
                }
                $dokumen['surat_pernyataan'] = $request->file('surat_pernyataan_hak_cipta')->store('dokumen_pernyataan', 'public');
                Log::info('New surat_pernyataan uploaded', ['file_path' => $dokumen['surat_pernyataan']]);
            }
            
            if ($request->hasFile('ktp_seluruh_pencipta')) {
                if (isset($dokumen['ktp'])) {
                    Storage::disk('public')->delete($dokumen['ktp']);
                }
                $dokumen['ktp'] = $request->file('ktp_seluruh_pencipta')->store('dokumen_ktp', 'public');
                Log::info('New KTP uploaded', ['file_path' => $dokumen['ktp']]);
            }
            
            // Validasi dokumen wajib - cek file yang sudah ada atau yang baru diupload
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
                Log::warning('Document validation failed', [
                    'error' => $error,
                    'file_karya' => $pengajuan->file_karya,
                    'dokumen' => $dokumen
                ]);
                return back()->withInput()->with('error', $error);
            }
            
            Log::info('All documents validated successfully');
            
            // Extract jumlah from "X orang" format
            $jumlahPenciptaNumber = (int) filter_var($validated['jumlah_pencipta'], FILTER_SANITIZE_NUMBER_INT);
            
            // Update data pengajuan
            $pengajuan->update([
                'judul_karya' => $validated['judul'],
                'deskripsi' => $validated['deskripsi'],
                'nama_pengusul' => $validated['nama_pengusul'],
                'nip_nidn' => $validated['nip_nidn'] ?? '',
                'no_hp' => $validated['no_hp'],
                'id_sinta' => $validated['id_sinta'] ?? '',
                'jumlah_pencipta' => $jumlahPenciptaNumber,
                'identitas_ciptaan' => $validated['identitas_ciptaan'],
                'sub_jenis_ciptaan' => $validated['sub_jenis_ciptaan'],
                'tanggal_pertama_kali_diumumkan' => $validated['tanggal_pertama_kali_diumumkan'],
                'tahun_usulan' => $validated['tahun_usulan'] ?? '',
                'role' => $validated['role'],
                'status' => 'menunggu_validasi',
                'file_dokumen_pendukung' => json_encode($dokumen),
            ]);
            
            Log::info('Pengajuan data updated', [
                'status' => 'menunggu_validasi',
                'jumlah_pencipta' => $jumlahPenciptaNumber
            ]);
            
            // Generate nomor pengajuan jika belum ada
            if (empty($pengajuan->nomor_pengajuan)) {
                $tahun = date('Y');
                $bulan = date('m');
                $prefix = sprintf('HKI/%s/%s/', $tahun, $bulan);
                
                $lastNumber = PengajuanHki::where('nomor_pengajuan', 'like', $prefix . '%')
                    ->selectRaw('CAST(SUBSTRING(nomor_pengajuan, -4) AS UNSIGNED) as urut')
                    ->orderBy('urut', 'desc')
                    ->value('urut');
                
                $nomorUrut = $lastNumber ? $lastNumber + 1 : 1;
                $nomorPengajuan = sprintf('HKI/%s/%s/%04d', $tahun, $bulan, $nomorUrut);
                
                $pengajuan->update([
                    'nomor_pengajuan' => $nomorPengajuan,
                    'tanggal_pengajuan' => Carbon::now('Asia/Makassar')
                ]);
                
                Log::info('Generated nomor_pengajuan', ['nomor' => $nomorPengajuan]);
            } else {
                $pengajuan->update(['tanggal_pengajuan' => Carbon::now('Asia/Makassar')]);
                Log::info('Updated tanggal_pengajuan', ['nomor' => $pengajuan->nomor_pengajuan]);
            }
            
            // Update data pencipta
            $pengajuan->pengaju()->delete();
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
            
            Log::info('Pencipta data updated');
            
            // Buat notifikasi
            Notifikasi::create([
                'user_id' => Auth::id(),
                'pengajuan_hki_id' => $pengajuan->id,
                'judul' => 'Pengajuan HKI Baru',
                'pesan' => 'Pengajuan HKI Anda dengan nomor "' . $pengajuan->nomor_pengajuan . '" telah berhasil dibuat dan sedang menunggu validasi.',
                'status' => 'unread',
                'dibaca' => false
            ]);
            
            Log::info('Notification created');
            
            // Clear cache 
            cache()->forget('pengajuan_user_' . Auth::id());
            
            Log::info('updateDraft redirect to pengajuan.index', [
                'pengajuan_id' => $pengajuan->id,
                'nomor_pengajuan' => $pengajuan->nomor_pengajuan,
                'status' => $pengajuan->status,
                'redirect_route' => route('pengajuan.index')
            ]);
            
            return Redirect::to(route('pengajuan.index'))->with('success', 'Draft berhasil dikirim dan menunggu validasi');
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