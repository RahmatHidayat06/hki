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


public function signatureForm($id)
{
    $pengajuan = PengajuanHki::findOrFail($id);

    // Pastikan dokumen Form Permohonan tersedia untuk pratinjau PDF
    $dokumen = is_string($pengajuan->file_dokumen_pendukung)
        ? json_decode($pengajuan->file_dokumen_pendukung, true)
        : ($pengajuan->file_dokumen_pendukung ?? []);

    $baseFormPath = $dokumen['form_permohonan_pendaftaran'] ?? null;
    $needGenerateForm = true;
    if ($baseFormPath) {
        $normalized = ltrim($baseFormPath, '/');
        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, strlen('storage/'));
        }
        if (\Storage::disk('public')->exists($normalized)) {
            $needGenerateForm = false;
        }
    }

    if ($needGenerateForm) {
        try {
            $suratController = new \App\Http\Controllers\SuratController();
            $generatedFormPath = $suratController->autoGenerateFormPermohonan($pengajuan);
            if ($generatedFormPath) {
                $dokumen['form_permohonan_pendaftaran'] = $generatedFormPath;
                $pengajuan->file_dokumen_pendukung = json_encode($dokumen);
                $pengajuan->save();
            }
        } catch (\Exception $e) {
            \Log::error('Gagal auto-generate Form Permohonan (render): ' . $e->getMessage());
        }
    }

    $formPath = $dokumen['form_permohonan_pendaftaran'] ?? null;
    $pdfUrl = $formPath ? \Storage::url(ltrim($formPath, '/')) : null;

    return view('pengajuan.signature', compact('pengajuan', 'pdfUrl'));
}

public function signatureSave(Request $request, $id)
{
    $pengajuan = PengajuanHki::findOrFail($id);
    $signatureData = $request->input('signature_data');
    $placementJson = $request->input('placement'); // optional koordinat drag-drop

    // Simpan signature ke storage
    if ($signatureData) {
        $image = str_replace('data:image/png;base64,', '', $signatureData);
        $image = str_replace(' ', '+', $image);
        $imageName = 'ttd_pengajuan_' . $id . '_' . time() . '.png';
        \Storage::disk('public')->put('ttd/' . $imageName, base64_decode($image));
        // Simpan path ke database (opsional untuk audit)
        $pengajuan->ttd_path = 'storage/ttd/' . $imageName;
        $pengajuan->save();
    }

    // Generate overlay untuk Form Permohonan Pendaftaran
    $dokumen = is_string($pengajuan->file_dokumen_pendukung) ? json_decode($pengajuan->file_dokumen_pendukung, true) : ($pengajuan->file_dokumen_pendukung ?? []);
    if (!isset($dokumen['overlays'])) $dokumen['overlays'] = [];

    // Default posisi yang sesuai dengan template form permohonan (bottom-right)
    $page = 1; $x = 70.0; $y = 80.0; $w = 20.0; $h = 8.0; $anchor = 'center';
    if ($placementJson) {
        try {
            $placement = json_decode($placementJson, true);
            if (is_array($placement)) {
                // Selalu hormati halaman dari frontend, default ke halaman 1 untuk Form Permohonan
                $page = (int)($placement['page'] ?? 1);
                $x = (float)($placement['x_percent'] ?? $x);
                $y = (float)($placement['y_percent'] ?? $y);
                $w = (float)($placement['width_percent'] ?? $w);
                $h = (float)($placement['height_percent'] ?? $h);
                $anchor = (string)($placement['anchor'] ?? $anchor);
            }
        } catch (\Throwable $e) {
            // abaikan, gunakan default
        }
    }

    if (isset($imageName)) {
        // HAPUS semua overlay lama untuk form_permohonan_pendaftaran untuk mencegah duplikasi
        unset($dokumen['overlays']['form_permohonan_pendaftaran']);
        
        // Buat overlay baru dengan posisi yang tepat
        $dokumen['overlays']['form_permohonan_pendaftaran'] = [
            [
                'url' => \Storage::url('ttd/' . $imageName),
                'page' => $page,
                'x_percent' => $x,
                'y_percent' => $y,
                'width_percent' => $w,
                'height_percent' => $h,
                'anchor' => $anchor
            ]
        ];
    }

    // Pastikan dokumen dasar form tersedia; jika tidak, auto-generate
    $baseFormPath = $dokumen['form_permohonan_pendaftaran'] ?? null;
    $needGenerateForm = true;
    if ($baseFormPath) {
        $normalized = ltrim($baseFormPath, '/');
        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, strlen('storage/'));
        }
        if (\Storage::disk('public')->exists($normalized)) {
            $needGenerateForm = false;
        }
    }
    if ($needGenerateForm) {
        try {
            $suratController = new \App\Http\Controllers\SuratController();
            $generatedFormPath = $suratController->autoGenerateFormPermohonan($pengajuan);
            if ($generatedFormPath) {
                $dokumen['form_permohonan_pendaftaran'] = $generatedFormPath;
            }
        } catch (\Exception $e) {
            \Log::error('Gagal auto-generate Form Permohonan: ' . $e->getMessage());
        }
    }
    $pengajuan->file_dokumen_pendukung = json_encode($dokumen);
    $pengajuan->save();

    // Lakukan SIGN PDF karena ini adalah alur khusus Form Permohonan (dipicu dari tombol form permohonan)
    try {
        $pdfSigner = new \App\Http\Controllers\PdfSigningController();
        $pdfSigner->signPdf($pengajuan, 'form_permohonan_pendaftaran');
    } catch (\Exception $e) {
        \Log::error('Gagal auto-sign Form Permohonan Pendaftaran: ' . $e->getMessage());
    }
    return redirect()->route('pengajuan.show', $id)
        ->with('success_signature_permohonan', 'Form Permohonan Pendaftaran berhasil ditandatangani!');
}
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
        // Check untuk action update
        if ($request->has('action') && $request->input('action') === 'update') {
            try {
            $validated = $request->validate([
                'judul' => 'nullable|string|max:255',
                'deskripsi' => 'nullable|string',
                'nama_pengusul' => 'nullable|string|max:255',
                'nip_nidn' => 'nullable|string|max:255',
                'no_telp' => 'nullable|string|max:255',
                'id_sinta' => 'nullable|string|max:255',
                    'jumlah_pencipta' => 'nullable|in:1,2,3,4,5',
                'identitas_ciptaan' => 'nullable|string|in:karya tulis,karya audio visual,karya lainnya',
                'sub_jenis_ciptaan' => 'nullable|string',
                'tanggal_pertama_kali_diumumkan' => 'nullable|date',
                'kota_pertama_kali_diumumkan' => 'nullable|string|max:255',
                'tahun_usulan' => 'nullable|string|max:10',
                    // Role not needed from input as it comes from auth()->user()->role
                'pencipta' => 'nullable|array',
                'pencipta.*.nama' => 'nullable|string|max:255',
                'pencipta.*.email' => 'nullable|email|max:255',
                'pencipta.*.no_telp' => 'nullable|string|max:255',
                'pencipta.*.alamat' => 'nullable|string',
                'pencipta.*.kewarganegaraan' => 'nullable|string|max:255',
                'pencipta.*.kodepos' => 'nullable|string|max:20',
                    'tanggal_surat' => 'nullable|date',
                    'alamat_pencipta' => 'nullable|array',
                    'alamat_pencipta.*.nama' => 'nullable|string|max:255',
                    'alamat_pencipta.*.gelar' => 'nullable|string|max:100',
                    'alamat_pencipta.*.alamat' => 'nullable|string',
                    'signature_pencipta' => 'nullable|array', 
                    'signature_pencipta.*.nama_ttd' => 'nullable|string|max:255',
                    'signature_pencipta.*.posisi' => 'nullable|string|in:kanan,kiri',
                    'gunakan_materai' => 'nullable|boolean',
                // dokumen dan pencipta dinamis tidak required saat draft
            ]);
            
                Log::info('Update pengajuan request', [
                'user_id' => Auth::id(),
                    'input' => $request->all()
                ]);

                // Buat pengajuan baru (draft)
                $pengajuan = new PengajuanHki();
                $pengajuan->user_id = Auth::id();
                $pengajuan->judul_karya = $validated['judul'] ?? '';
                $pengajuan->deskripsi = $validated['deskripsi'] ?? '';
                $pengajuan->nama_pengusul = $validated['nama_pengusul'] ?? auth()->user()->nama_lengkap;
                $pengajuan->nip_nidn = auth()->user()->nip_nidn ?? '';
                $pengajuan->no_telp = $validated['no_telp'] ?? '';
                $pengajuan->id_sinta = auth()->user()->id_sinta ?? '';
                $pengajuan->jumlah_pencipta = $validated['jumlah_pencipta'] ?? '';
                $pengajuan->identitas_ciptaan = $validated['identitas_ciptaan'] ?? '';
                $pengajuan->sub_jenis_ciptaan = $validated['sub_jenis_ciptaan'] ?? '';
                $pengajuan->tanggal_pertama_kali_diumumkan = $validated['tanggal_pertama_kali_diumumkan'] ?? '';
                $pengajuan->kota_pertama_kali_diumumkan = $validated['kota_pertama_kali_diumumkan'] ?? '';
                $pengajuan->status = 'draft';
                $pengajuan->role = auth()->user()->role;
                $pengajuan->tanggal_surat = $validated['tanggal_surat'] ?? null;
                $pengajuan->alamat_pencipta = $validated['alamat_pencipta'] ?? null;
                $pengajuan->signature_pencipta = $validated['signature_pencipta'] ?? null;
                $pengajuan->gunakan_materai = $validated['gunakan_materai'] ?? false;
                $pengajuan->save();
            
                Log::info('Pengajuan draft created', [
                    'id' => $pengajuan->id,
                    'user_id' => $pengajuan->user_id,
                'status' => $pengajuan->status
            ]);
            
                return Redirect::to(route('pengajuan.draftIndex'))->with('success', 'Pengajuan berhasil disimpan sebagai draft');
            } catch (ValidationException $e) {
                Log::error('Validation error during draft update', [
                    'errors' => $e->errors(),
                    'user_id' => Auth::id(),
                    'request_data' => $request->all()
                ]);
                return Redirect::back()->withErrors($e->errors())->withInput();
            } catch (\Exception $e) {
                Log::error('Error creating draft pengajuan', [
                    'error' => $e->getMessage(),
                    'user_id' => Auth::id(),
                    'trace' => $e->getTraceAsString()
                ]);
                return Redirect::back()->with('error', 'Terjadi kesalahan saat menyimpan draft')->withInput();
            }
        }

        // Check untuk action submit
        if ($request->input('action') === 'submit') {
            try {
                // Get user role for dynamic validation
                $userRole = auth()->user()->role;
                
                $validationRules = [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
                    'nama_pengusul' => 'nullable|string|max:255',
                    'no_telp' => 'nullable|string|max:255',
                'jumlah_pencipta' => 'required|in:1,2,3,4,5',
            'identitas_ciptaan' => 'required|string|in:karya tulis,karya audio visual,karya lainnya',
            'sub_jenis_ciptaan' => 'required|string|in:Buku,E-Book,Diktat,Modul,Buku Panduan/Petunjuk,Karya Ilmiah,Karya Tulis/Artikel,Laporan Penelitian,Jurnal,Kuliah,Karya Rekaman Video,Karya Siaran Video,Program Komputer,Permainan Video,Basis Data',
            'tanggal_pertama_kali_diumumkan' => 'required|date',
            'kota_pertama_kali_diumumkan' => 'required|string|max:255',
            'contoh_ciptaan_type' => 'required|in:upload,link',
            'contoh_ciptaan' => 'required_if:contoh_ciptaan_type,upload|file|mimes:pdf,mp4,mp3,jpg,jpeg,png,gif,svg,webp,doc,docx|max:10240',
            'contoh_ciptaan_link' => 'required_if:contoh_ciptaan_type,link|nullable|url',
            'pencipta' => 'required|array',
            'pencipta.*.nama' => 'required|string|max:255',
            'pencipta.*.email' => 'required|email|max:255',
            'pencipta.*.no_telp' => 'nullable|string|max:255',
            'pencipta.*.alamat' => 'nullable|string',
            'pencipta.*.kewarganegaraan' => 'nullable|string|max:255',
            'pencipta.*.kodepos' => 'nullable|string|max:20',
            'tahun_usulan' => 'nullable|string|max:10',
                    'tanggal_surat' => 'required|date',
                    'alamat_pencipta' => 'required|array',
                    'alamat_pencipta.*.nama' => 'required|string|max:255',
                    'alamat_pencipta.*.gelar' => 'nullable|string|max:100',
                    'alamat_pencipta.*.alamat' => 'required|string',
                    'signature_pencipta' => 'required|array', 
                    'signature_pencipta.*.nama_ttd' => 'required|string|max:255',
                    'signature_pencipta.*.posisi' => 'nullable|string|in:kanan,kiri',
                    'gunakan_materai' => 'nullable|boolean',
                ];
                
                // Note: nip_nidn and id_sinta are now handled during registration
                
                $validationMessages = [
            'judul.required' => 'Judul karya wajib diisi.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'nama_pengusul.required' => 'Nama pengusul wajib diisi.',
            'no_telp.required' => 'Nomor HP wajib diisi.',
            'jumlah_pencipta.required' => 'Jumlah pencipta wajib diisi.',
            'identitas_ciptaan.required' => 'Jenis ciptaan wajib diisi.',
            'sub_jenis_ciptaan.required' => 'Sub jenis ciptaan wajib diisi.',
            'tanggal_pertama_kali_diumumkan.required' => 'Tanggal pertama kali diumumkan wajib diisi.',
            'contoh_ciptaan_type.required' => 'Pilih tipe contoh ciptaan (upload atau link).',
            'contoh_ciptaan.required_if' => 'File contoh ciptaan wajib diupload.',
            'contoh_ciptaan_link.required_if' => 'Link contoh ciptaan wajib diisi.',
            
            'pencipta.required' => 'Data pencipta wajib diisi minimal satu.',
            'pencipta.*.nama.required' => 'Nama setiap pencipta wajib diisi.',
            'pencipta.*.email.required' => 'Email setiap pencipta wajib diisi.',
                    'tanggal_surat.required' => 'Tanggal surat wajib diisi.',
                    'alamat_pencipta.required' => 'Alamat pencipta wajib diisi.',
                    'alamat_pencipta.*.nama.required' => 'Nama lengkap setiap pencipta wajib diisi.',
                    'alamat_pencipta.*.alamat.required' => 'Alamat lengkap setiap pencipta wajib diisi.',
                    'signature_pencipta.required' => 'Detail tanda tangan pencipta wajib diisi.',
                    'signature_pencipta.*.nama_ttd.required' => 'Nama untuk tanda tangan setiap pencipta wajib diisi.',
                ];
                
                $validated = $request->validate($validationRules, $validationMessages);
                
        // Proses file atau link contoh ciptaan
        $fileKarya = null;
        if ($request->input('contoh_ciptaan_type') === 'upload' && $request->hasFile('contoh_ciptaan')) {
            $fileKarya = $request->file('contoh_ciptaan')->store('dokumen_ciptaan', 'public');
        } elseif ($request->input('contoh_ciptaan_type') === 'link') {
            $fileKarya = $request->input('contoh_ciptaan_link');
        }

        // KTP akan diupload melalui proses tanda tangan

        // Inisialisasi dokumen pendukung minimal (KTP akan ditambahkan saat proses tanda tangan)
        $initialDokumenPendukung = json_encode([]);

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
            'nama_pengusul' => $validated['nama_pengusul'] ?? auth()->user()->nama_lengkap,
                    'nip_nidn' => auth()->user()->nip_nidn ?? '',
            'no_telp' => $validated['no_telp'] ?? '',
                    'id_sinta' => auth()->user()->id_sinta ?? '',
            'jumlah_pencipta' => $validated['jumlah_pencipta'] ?? '',
            'identitas_ciptaan' => $validated['identitas_ciptaan'] ?? '',
            'sub_jenis_ciptaan' => $validated['sub_jenis_ciptaan'] ?? '',
            'tanggal_pertama_kali_diumumkan' => $validated['tanggal_pertama_kali_diumumkan'] ?? '',
            'kota_pertama_kali_diumumkan' => $validated['kota_pertama_kali_diumumkan'] ?? '',
            'status' => 'menunggu_tanda_tangan',
            'tanggal_pengajuan' => Carbon::now('Asia/Makassar'),
            'nomor_pengajuan' => $nomorPengajuan,
                    'role' => auth()->user()->role,
            'file_karya' => $fileKarya,
            'tahun_usulan' => $validated['tahun_usulan'] ?? '',
                    'tanggal_surat' => $validated['tanggal_surat'] ?? null,
                    'alamat_pencipta' => $validated['alamat_pencipta'] ?? null,
                    'signature_pencipta' => $validated['signature_pencipta'] ?? null,
                    'gunakan_materai' => $validated['gunakan_materai'] ?? false,
            'file_dokumen_pendukung' => $initialDokumenPendukung,
        ]);

        // Simpan data pencipta dinamis
        if (isset($validated['pencipta'])) {
            foreach ($validated['pencipta'] as $dataPencipta) {
                if (!empty($dataPencipta['email']) && !empty($dataPencipta['nama'])) {
                    $pengajuan->pengaju()->create([
                        'nama' => $dataPencipta['nama'],
                        'email' => $dataPencipta['email'],
                        'no_telp' => $dataPencipta['no_telp'] ?? null,
                        'alamat' => $dataPencipta['alamat'] ?? null,
                        'kewarganegaraan' => $dataPencipta['kewarganegaraan'] ?? null,
                        'kodepos' => $dataPencipta['kodepos'] ?? null,
                        'no_telp' => $dataPencipta['no_telp'] ?? $dataPencipta['no_telp'] ?? null,
                    ]);
                }
            }
        }

                // Auto-generate surat berdasarkan data pengajuan
                $suratController = new \App\Http\Controllers\SuratController();
                $generatedPaths = $suratController->autoGenerateAllDocuments($pengajuan);

                // Update file_dokumen_pendukung dengan path auto-generated (KTP akan ditambahkan saat tanda tangan)
                $dokumenPendukung = [];
                if (!empty($generatedPaths['form_permohonan_pendaftaran'])) {
                    $dokumenPendukung['form_permohonan_pendaftaran'] = $generatedPaths['form_permohonan_pendaftaran'];
                }
                if (!empty($generatedPaths['surat_pengalihan'])) {
                    $dokumenPendukung['surat_pengalihan'] = $generatedPaths['surat_pengalihan'];
                }
                if (!empty($generatedPaths['surat_pernyataan'])) {
                    $dokumenPendukung['surat_pernyataan'] = $generatedPaths['surat_pernyataan'];
                }
                $pengajuan->update([
                    'file_dokumen_pendukung' => json_encode($dokumenPendukung)
                ]);

        Notifikasi::create([
            'user_id' => Auth::id(),
            'pengajuan_hki_id' => $pengajuan->id,
            'judul' => 'Pengajuan HKI Baru',
                    'pesan' => 'Pengajuan HKI Anda dengan nomor "' . $nomorPengajuan . '" telah berhasil dibuat dengan surat otomatis dan sedang menunggu validasi.',
            'status' => 'unread',
            'dibaca' => false
        ]);

        // Create initial tracking status
        $pengajuan->addTracking(
            'submitted',
            'Pengajuan Diterima',
            'Pengajuan HKI telah diterima dan menunggu validasi P3M',
            'fas fa-file-upload',
            'primary',
            'Pengajuan berhasil disubmit ke sistem'
        );

        // Create signatures untuk setiap pencipta
        if (isset($validated['signature_pencipta']) && is_array($validated['signature_pencipta'])) {
            $penciptaData = [];
            // Normalisasi agar indeks dimulai dari 0 berurutan (hindari offset)
            $signatureArr = array_values($validated['signature_pencipta']);
            $alamatArr    = array_values($validated['alamat_pencipta'] ?? []);
            $penciptaArr  = array_values($validated['pencipta'] ?? []);

            foreach ($signatureArr as $idx => $signatureData) {
                $alamatPencipta = $alamatArr[$idx]    ?? [];
                $penciptaDb     = $penciptaArr[$idx]  ?? [];
                
                $penciptaData[] = [
                    'nama'   => $alamatPencipta['nama'] ?? ($penciptaDb['nama'] ?? 'Pencipta ' . ($idx + 1)),
                    'email'  => $penciptaDb['email']    ?? null,
                    'nama_ttd' => $signatureData['nama_ttd'],
                    'posisi'   => $signatureData['posisi'] ?? 'kanan'
                ];
            }
            
            \App\Models\Signature::createSignaturesForPengajuan($pengajuan->id, $penciptaData);
            
            // Update status jika ada multiple signatures
            if (count($penciptaData) > 1) {
                $pengajuan->update(['status' => 'menunggu_tanda_tangan']);
                $pengajuan->addTracking(
                    'waiting_signatures',
                    'Menunggu Tanda Tangan',
                    'Dokumen telah digenerate dan menunggu tanda tangan dari semua pencipta',
                    'fas fa-signature',
                    'warning',
                    'Total ' . count($penciptaData) . ' pencipta perlu menandatangani'
                );
            }
        }

        // Clear cache jika ada
        if (function_exists('cache')) {
            cache()->forget('pengajuan_user_' . Auth::id());
        }
        
                return Redirect::to(route('pengajuan.index'))->with('success', 'Pengajuan berhasil dibuat dengan surat otomatis');
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
                'trace' => $e->getTraceAsString()
            ]);
                return Redirect::back()->with('error', 'Terjadi kesalahan saat membuat pengajuan')->withInput();
        }
        }

        // Default fallback (should not reach here in normal flow)
        return Redirect::back()->with('error', 'Action tidak valid')->withInput();
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

        // Load relasi yang diperlukan
        $pengajuan->load(['user', 'pengaju']);

        // Proses dokumen pendukung untuk menampilkan file terbaru/bertanda tangan
        $dokumen = [];
        if($pengajuan->file_dokumen_pendukung){
            $dokumen = is_string($pengajuan->file_dokumen_pendukung)
                ? json_decode($pengajuan->file_dokumen_pendukung, true)
                : $pengajuan->file_dokumen_pendukung;
        }
        
        $pencipta = $pengajuan->pengaju;
        $preferSigned = in_array($pengajuan->status, ['divalidasi_sedang_diproses','menunggu_pembayaran','menunggu_verifikasi_pembayaran','selesai']);

        // Konfigurasi dokumen dengan prioritas file bertanda tangan
        $ktpGabunganPath = $dokumen['ktp_gabungan'] ?? $dokumen['ktp'] ?? null;
        $ktpFileInfo = $ktpGabunganPath ? $this->getFileInfoFromDokumen($dokumen, 'ktp_gabungan', $preferSigned) : null;
        $documents = [
            'contoh_ciptaan' => [
                'label'=>'Contoh Ciptaan',
                'description'=>'File contoh karya yang diajukan',
                'icon'=>'fas fa-palette',
                'color'=>'primary',
                'file_info'=>$this->getFileInfoFromFileKarya($pengajuan->file_karya)
            ],
            'form_permohonan_pendaftaran' => [
                'label' => 'Form Permohonan Pendaftaran',
                'description' => 'Form permohonan pendaftaran ciptaan',
                'icon' => 'fas fa-file-contract',
                'color' => 'primary',
                'file_info' => $this->getFileInfoFromDokumen($dokumen, 'form_permohonan_pendaftaran', $preferSigned)
            ],
            'surat_pengalihan' => [
                'label'=>'Surat Pengalihan Hak',
                'description'=>'Dokumen pengalihan hak cipta',
                'icon'=>'fas fa-exchange-alt',
                'color'=>'info',
                'file_info'=>$this->getFileInfoFromDokumen($dokumen,'surat_pengalihan',$preferSigned)
            ],
            'surat_pernyataan' => [
                'label'=>'Surat Pernyataan',
                'description'=>'Surat pernyataan keaslian karya',
                'icon'=>'fas fa-file-signature',
                'color'=>'warning',
                'file_info'=>$this->getFileInfoFromDokumen($dokumen,'surat_pernyataan',$preferSigned)
            ],
            'ktp_gabungan'=>[
                'label'=>'KTP Gabungan',
                'description'=>'Kartu Tanda Penduduk Gabungan',
                'icon'=>'fas fa-id-card',
                'color'=>'success',
                'file_info'=>$ktpFileInfo
            ],
        ];

        return view('pengajuan.show', compact('pengajuan', 'dokumen', 'pencipta', 'documents'));
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
        if ($pengajuan->status !== 'menunggu_validasi_direktur') {
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

        if ($pengajuan->status !== 'menunggu_validasi_direktur') {
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
                'pencipta.*.no_telp' => 'nullable|string|max:255',
                'pencipta.*.alamat' => 'nullable|string',
                'pencipta.*.kewarganegaraan' => 'nullable|string|max:255',
                'pencipta.*.kodepos' => 'nullable|string|max:20',
            ], [
                'nama_pengusul.required' => 'Nama pengusul wajib diisi.',
                'no_telp.required' => 'Nomor HP wajib diisi.',
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
                    foreach (['nama','email','no_telp','alamat','kewarganegaraan','kodepos'] as $f) {
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
                'status' => 'draft',
            ]);
            $pengajuan->pengaju()->delete();
            if (isset($validated['pencipta'])) {
                foreach ($validated['pencipta'] as $dataPencipta) {
                    if (!empty($dataPencipta['nama']) && !empty($dataPencipta['email'])) {
                        $pengajuan->pengaju()->create([
                            'nama' => $dataPencipta['nama'] ?? null,
                            'email' => $dataPencipta['email'] ?? null,
                            'no_telp' => $dataPencipta['no_telp'] ?? null,
                            'alamat' => $dataPencipta['alamat'] ?? null,
                            'kewarganegaraan' => $dataPencipta['kewarganegaraan'] ?? null,
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
            'pencipta.*.no_telp' => 'nullable|string|max:255',
            'pencipta.*.alamat' => 'nullable|string',
            'pencipta.*.kewarganegaraan' => 'nullable|string|max:255',
            'pencipta.*.kodepos' => 'nullable|string|max:20',
        ]);

        $pengajuan->update([
            'judul_karya' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'identitas_ciptaan' => $validated['identitas_ciptaan'],
            'sub_jenis_ciptaan' => $validated['sub_jenis_ciptaan'],
            'status' => 'menunggu_validasi_direktur',
        ]);

        // Update data pencipta
        $pengajuan->pengaju()->delete();
        foreach ($validated['pencipta'] as $dataPencipta) {
            if (!empty($dataPencipta['email']) && !empty($dataPencipta['nama'])) {
                $pengajuan->pengaju()->create([
                    'nama' => $dataPencipta['nama'],
                    'email' => $dataPencipta['email'],
                    'no_telp' => $dataPencipta['no_telp'] ?? null,
                    'alamat' => $dataPencipta['alamat'] ?? null,
                    'kewarganegaraan' => $dataPencipta['kewarganegaraan'] ?? null,
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
        if ($pengajuan->status !== 'menunggu_validasi_direktur') {
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
            'nama_pengusul' => auth()->user()->nama_lengkap ?? '',
            'nip_nidn' => $request->input('nip_nidn', ''),
            'no_telp' => auth()->user()->no_telp ?? '',
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
                'status' => 'menunggu_validasi_direktur',
                'nomor_pengajuan' => $nomorPengajuan,
                'tanggal_pengajuan' => Carbon::now('Asia/Makassar')
            ]);
        } else {
            $pengajuan->update(['status' => 'menunggu_validasi_direktur']);
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
                'no_telp' => $request->input('no_telp', $pengajuan->no_telp),
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
            // KTP akan diupload melalui proses tanda tangan
            
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
                            'no_telp' => $dataPencipta['no_telp'] ?? null,
                            'alamat' => $dataPencipta['alamat'] ?? null,
                            'kewarganegaraan' => $dataPencipta['kewarganegaraan'] ?? null,
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
                'nama_pengusul' => 'nullable|string|max:255',
                'no_telp' => 'nullable|string|max:255',
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
                'pencipta.*.no_telp' => 'nullable|string|max:255',
                'pencipta.*.alamat' => 'nullable|string',
                'pencipta.*.kewarganegaraan' => 'nullable|string|max:255',
                'pencipta.*.kodepos' => 'nullable|string|max:20',
                'tahun_usulan' => 'nullable|string|max:10',
            ], [
                'judul.required' => 'Judul karya wajib diisi.',
                'deskripsi.required' => 'Deskripsi wajib diisi.',
                'nama_pengusul.required' => 'Nama pengusul wajib diisi.',
                'no_telp.required' => 'Nomor HP wajib diisi.',
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
                'nama_pengusul' => $validated['nama_pengusul'] ?? auth()->user()->nama_lengkap,
                'nip_nidn' => auth()->user()->nip_nidn ?? '',
                'no_telp' => $validated['no_telp'],
                'id_sinta' => auth()->user()->id_sinta ?? '',
                'jumlah_pencipta' => $jumlahPenciptaNumber,
                'identitas_ciptaan' => $validated['identitas_ciptaan'],
                'sub_jenis_ciptaan' => $validated['sub_jenis_ciptaan'],
                'tanggal_pertama_kali_diumumkan' => $validated['tanggal_pertama_kali_diumumkan'],
                'tahun_usulan' => $validated['tahun_usulan'] ?? '',
                'role' => $validated['role'],
                'status' => 'menunggu_validasi_direktur',
                'file_dokumen_pendukung' => json_encode($dokumen),
            ]);
            
            Log::info('Pengajuan data updated', [
                'status' => 'menunggu_validasi_direktur',
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
                            'no_telp' => $dataPencipta['no_telp'] ?? null,
                            'alamat' => $dataPencipta['alamat'] ?? null,
                            'kewarganegaraan' => $dataPencipta['kewarganegaraan'] ?? null,
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

    /* --------- Helper methods for file processing --------- */

    private function getFileInfoFromDokumen($dokumen, $key, bool $preferSigned = false)
    {
        if (!is_array($dokumen)) {
            return [
                'exists' => false,
                'url' => null,
                'filename' => null,
                'extension' => null,
                'size' => null,
                'is_signed' => false,
                'original_exists' => false,
                'signed_exists' => false
            ];
        }

        $originalFile = $dokumen[$key] ?? null;
        $signedFile = $dokumen['signed'][$key] ?? null;
        
        // Tentukan file mana yang akan digunakan
        $useSignedFile = $preferSigned && $signedFile;
        $selectedFile = $useSignedFile ? $signedFile : $originalFile;
        
        if (!$selectedFile) {
            return [
                'exists' => false,
                'url' => null,
                'filename' => null,
                'extension' => null,
                'size' => null,
                'is_signed' => false,
                'original_exists' => (bool)$originalFile,
                'signed_exists' => (bool)$signedFile
            ];
        }

        // Dapatkan info file utama
        $fileInfo = $this->getFileInfo($selectedFile);
        if (!$fileInfo) {
            return [
                'exists' => false,
                'url' => null,
                'filename' => null,
                'extension' => null,
                'size' => null,
                'is_signed' => false,
                'original_exists' => (bool)$originalFile,
                'signed_exists' => (bool)$signedFile
            ];
        }

        // Cek keberadaan file original dan signed
        $originalExists = $originalFile ? $this->fileExists($originalFile) : false;
        $signedExists = $signedFile ? $this->fileExists($signedFile) : false;
        
        return [
            'exists' => true,
            'url' => $fileInfo['url'],
            'filename' => $fileInfo['filename'],
            'extension' => $fileInfo['extension'],
            'size' => $fileInfo['size'],
            'is_signed' => $useSignedFile,
            'original_exists' => $originalExists,
            'signed_exists' => $signedExists,
            'original_url' => $originalExists && $originalFile ? $this->getFileUrl($originalFile) : null,
            'signed_url' => $signedExists && $signedFile ? $this->getFileUrl($signedFile) : null,
            'original_filename' => $originalFile ? basename($originalFile) : null,
            'signed_filename' => $signedFile ? basename($signedFile) : null
        ];
    }

    private function getFileInfoFromFileKarya($fileKarya)
    {
        if (!$fileKarya) {
            return [
                'exists' => false,
                'url' => null,
                'filename' => null,
                'extension' => null,
                'size' => null,
                'is_signed' => false
            ];
        }

        // Jika URL eksternal, kembalikan info dasar
        if (filter_var($fileKarya, FILTER_VALIDATE_URL)) {
            return [
                'exists' => true,
                'url' => $fileKarya,
                'filename' => basename($fileKarya),
                'extension' => strtoupper(pathinfo($fileKarya, PATHINFO_EXTENSION)),
                'size' => 0,
                'is_signed' => false // File karya tidak ditandatangani
            ];
        }

        $fileInfo = $this->getFileInfo($fileKarya);
        if (!$fileInfo) {
            return [
                'exists' => false,
                'url' => null,
                'filename' => null,
                'extension' => null,
                'size' => null,
                'is_signed' => false
            ];
        }

        return [
            'exists' => true,
            'url' => $fileInfo['url'],
            'filename' => $fileInfo['filename'],
            'extension' => $fileInfo['extension'],
            'size' => $fileInfo['size'],
            'is_signed' => false // File karya tidak ditandatangani
        ];
    }

    private function fileExists($filePath)
    {
        if (!$filePath) return false;
        
        // Normalize path
        $normalized = ltrim($filePath, '/');
        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, strlen('storage/'));
        }
        
        return Storage::disk('public')->exists($normalized);
    }

    private function getFileUrl($filePath)
    {
        if (!$filePath) return null;
        
        // Jika sudah URL, return as is
        if (filter_var($filePath, FILTER_VALIDATE_URL)) {
            return $filePath;
        }
        
        // Normalize path dan return storage URL
        $normalized = ltrim($filePath, '/');
        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, strlen('storage/'));
        }
        
        return Storage::url($normalized);
    }

    private function getFileInfo($filePath)
    {
        if (!$filePath) return null;
        
        // Normalize various forms: leading slash, /storage/, full URL
        $normalized = ltrim($filePath, '/');
        if(!Storage::disk('public')->exists($normalized)){
            // Strip 'storage/' prefix if present
            if(str_starts_with($normalized, 'storage/')){
                $maybe = substr($normalized, strlen('storage/'));
                if(Storage::disk('public')->exists($maybe)){
                    $normalized = $maybe;
                }
            } elseif(filter_var($normalized, FILTER_VALIDATE_URL)){
                $maybeRel = $this->storageUrlToRelative($normalized);
                if($maybeRel && Storage::disk('public')->exists($maybeRel)){
                    $normalized = $maybeRel;
                }
            }
        }

        if (!Storage::disk('public')->exists($normalized)) return null;

        $size = Storage::disk('public')->size($normalized);
        $extension = pathinfo($normalized, PATHINFO_EXTENSION);

        return [
            'size' => $size,
            'size_formatted' => $this->formatFileSize($size),
            'extension' => strtoupper($extension),
            'filename' => basename($normalized),
            'icon_class' => $this->getFileTypeIcon($extension),
            'url' => Storage::url($normalized),
        ];
    }

    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }

    private function getFileTypeIcon($extension)
    {
        $ext = strtolower($extension);
        return match($ext) {
            'pdf' => 'fas fa-file-pdf text-danger',
            'doc','docx' => 'fas fa-file-word text-primary',
            'xls','xlsx','csv' => 'fas fa-file-excel text-success',
            'ppt','pptx' => 'fas fa-file-powerpoint text-warning',
            'jpg','jpeg','png','gif','svg','webp' => 'fas fa-file-image text-info',
            default => 'fas fa-file'
        };
    }

    private function storageUrlToRelative(string $url): ?string
    {
        $storageUrl = Storage::url('');
        if (str_starts_with($url, $storageUrl)) {
            return substr($url, strlen($storageUrl));
        }
        return null;
    }

    public function konfirmasiSelesaiTtd($id)
    {
        $pengajuan = \App\Models\PengajuanHki::findOrFail($id);
        // Cegah pengiriman ulang jika sudah dikirim ke Direktur atau sudah melampaui tahap tersebut
        if (in_array($pengajuan->status, [
            'menunggu_validasi_direktur',
            'divalidasi_sedang_diproses',
            'menunggu_pembayaran',
            'menunggu_verifikasi_pembayaran',
            'selesai',
            'ditolak'
        ])) {
            return back()->with('error', 'Pengajuan ini sudah dikirim/ diproses. Konfirmasi hanya bisa dilakukan satu kali.');
        }
        if (!$pengajuan->allSignaturesSigned()) {
            return back()->with('error', 'Semua dokumen harus sudah ditandatangani.');
        }
        $pengajuan->status = 'menunggu_validasi_direktur';
        $pengajuan->save();
        // Kirim notifikasi ke direktur
        $direkturs = \App\Models\User::where('role', 'direktur')->get();
        foreach ($direkturs as $direktur) {
            \App\Models\Notifikasi::create([
                'user_id' => $direktur->id,
                'pengajuan_hki_id' => $pengajuan->id,
                'judul' => 'Pengajuan HKI Siap Validasi',
                'pesan' => 'Pengajuan HKI dengan judul "' . $pengajuan->judul_karya . '" sudah siap divalidasi dan ditandatangani.',
                'status' => 'unread',
                'dibaca' => false
            ]);
        }
        return back()->with('success', 'Pengajuan berhasil dikirim ke Direktur untuk validasi.');
    }

    /**
     * Upload KTP Pemohon (oleh Admin P3M)
     */
    public function uploadKtpPemohon(Request $request, $id)
    {
        $pengajuan = PengajuanHki::findOrFail($id);
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya admin P3M yang dapat mengupload KTP pemohon');
        }
        $request->validate([
            'ktp_pemohon' => 'required|image|mimes:jpeg,jpg,png|max:5120',
        ]);
        $ktpPath = $request->file('ktp_pemohon')->store('ktp_pemohon', 'public');
        $dokumen = is_string($pengajuan->file_dokumen_pendukung) ? json_decode($pengajuan->file_dokumen_pendukung, true) : ($pengajuan->file_dokumen_pendukung ?? []);
        $dokumen['ktp_pemohon'] = $ktpPath;
        $pengajuan->file_dokumen_pendukung = json_encode($dokumen);
        $pengajuan->save();
        // Regenerate KTP Gabungan
        app(\App\Http\Controllers\MultiSignatureController::class)->generateCombinedKtpDocument($pengajuan->id);
        return back()->with('success', 'KTP Pemohon berhasil diupload dan KTP Gabungan diperbarui.');
    }

    /**
     * Upload KTP Pemegang Hak Cipta (oleh Direktur)
     */
    public function uploadKtpPemegangHakCipta(Request $request, $id)
    {
        $pengajuan = PengajuanHki::findOrFail($id);
        if (auth()->user()->role !== 'direktur') {
            abort(403, 'Hanya direktur yang dapat mengupload KTP pemegang hak cipta');
        }
        $request->validate([
            'ktp_pemegang_hak' => 'required|image|mimes:jpeg,jpg,png|max:5120',
        ]);
        $ktpPath = $request->file('ktp_pemegang_hak')->store('ktp_pemegang_hak', 'public');
        $dokumen = is_string($pengajuan->file_dokumen_pendukung) ? json_decode($pengajuan->file_dokumen_pendukung, true) : ($pengajuan->file_dokumen_pendukung ?? []);
        $dokumen['ktp_pemegang_hak'] = $ktpPath;
        $pengajuan->file_dokumen_pendukung = json_encode($dokumen);
        $pengajuan->save();
        // Pastikan re-generate KTP Gabungan dan refresh JSON dokumen signed
        try {
            $combined = app(\App\Http\Controllers\MultiSignatureController::class)->generateCombinedKtpDocument($pengajuan->id);
            if ($combined) {
                // muat ulang dari DB agar view mendapat path terbaru
                $pengajuan->refresh();
                return back()->with('success', 'KTP Pemegang Hak Cipta berhasil diupload dan KTP Gabungan diperbarui.');
            }
        } catch (\Throwable $e) {
            \Log::error('Regenerate KTP Gabungan gagal setelah upload direktur', ['pengajuan_id'=>$pengajuan->id, 'error'=>$e->getMessage()]);
        }
        return back()->with('error', 'KTP terupload, namun KTP Gabungan gagal diperbarui. Coba ulangi atau hubungi admin.');
    }
} 