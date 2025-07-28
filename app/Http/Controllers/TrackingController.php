<?php

namespace App\Http\Controllers;

use App\Models\PengajuanHki;
use App\Models\TrackingStatus;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    /**
     * Tampilkan tracking status untuk pengajuan
     */
    public function show($pengajuanId)
    {
        $pengajuan = PengajuanHki::with(['trackingStatuses.user', 'signatures', 'user'])->findOrFail($pengajuanId);
        
        // Check permission
        if (!$this->canViewTracking($pengajuan)) {
            abort(403, 'Tidak memiliki akses untuk melihat tracking ini');
        }

        $trackingStatuses = $pengajuan->trackingStatuses;
        $signatureProgress = $pengajuan->getSignatureProgress();

        return view('tracking.show', compact('pengajuan', 'trackingStatuses', 'signatureProgress'));
    }

    /**
     * Get tracking data untuk AJAX
     */
    public function getTrackingData($pengajuanId)
    {
        $pengajuan = PengajuanHki::with(['trackingStatuses.user'])->findOrFail($pengajuanId);
        
        if (!$this->canViewTracking($pengajuan)) {
            abort(403);
        }

        $trackingData = $pengajuan->trackingStatuses->map(function($tracking) {
            return [
                'id' => $tracking->id,
                'status' => $tracking->status,
                'title' => $tracking->title,
                'description' => $tracking->description,
                'icon' => $tracking->icon,
                'color' => $tracking->color,
                'created_at' => $tracking->created_at->format('d/m/Y H:i'),
                'created_at_human' => $tracking->created_at->diffForHumans(),
                'user_name' => $tracking->user?->nama_lengkap ?? 'System',
                'notes' => $tracking->notes
            ];
        });

        return response()->json([
            'tracking' => $trackingData,
            'signature_progress' => $pengajuan->getSignatureProgress(),
            'current_status' => $pengajuan->status
        ]);
    }

    /**
     * Tampilkan tracking dalam format timeline untuk public (dengan token)
     */
    public function publicTracking($pengajuanId, $token = null)
    {
        $pengajuan = PengajuanHki::with(['trackingStatuses.user', 'signatures'])->findOrFail($pengajuanId);
        
        // Untuk public tracking, bisa menggunakan token atau nomor pengajuan
        if (!$this->canViewPublicTracking($pengajuan, $token)) {
            abort(403, 'Akses tidak valid');
        }

        $trackingStatuses = $pengajuan->trackingStatuses;
        $signatureProgress = $pengajuan->getSignatureProgress();

        return view('tracking.public', compact('pengajuan', 'trackingStatuses', 'signatureProgress'));
    }

    /**
     * Create initial tracking status untuk pengajuan baru
     */
    public function createInitialTracking($pengajuanId)
    {
        $pengajuan = PengajuanHki::findOrFail($pengajuanId);
        
        // Create initial tracking
        $pengajuan->addTracking(
            'submitted',
            'Pengajuan Diterima',
            'Pengajuan HKI telah diterima dan menunggu validasi P3M',
            'fas fa-file-upload',
            'primary',
            'Pengajuan berhasil disubmit ke sistem'
        );

        return response()->json(['message' => 'Initial tracking created']);
    }

    /**
     * Update tracking status (untuk use di controller lain)
     */
    public function updateTracking(Request $request, $pengajuanId)
    {
        $request->validate([
            'status' => 'required|string',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $pengajuan = PengajuanHki::findOrFail($pengajuanId);
        
        if (!$this->canUpdateTracking($pengajuan)) {
            abort(403, 'Tidak memiliki akses untuk update tracking');
        }

        $pengajuan->addTracking(
            $request->status,
            $request->title,
            $request->description,
            $request->icon ?? 'fas fa-circle',
            $request->color ?? 'secondary',
            $request->notes
        );

        return response()->json(['message' => 'Tracking updated successfully']);
    }

    /**
     * Get tracking summary untuk dashboard
     */
    public function getTrackingSummary()
    {
        $user = auth()->user();
        $pengajuanIds = [];

        if ($user->isAdmin()) {
            $pengajuanIds = PengajuanHki::pluck('id');
        } else {
            $pengajuanIds = PengajuanHki::where('user_id', $user->id)->pluck('id');
        }

        $recentTracking = TrackingStatus::whereIn('pengajuan_hki_id', $pengajuanIds)
            ->with(['pengajuanHki', 'user'])
            ->latest()
            ->limit(10)
            ->get();

        return response()->json($recentTracking->map(function($tracking) {
            return [
                'title' => $tracking->title,
                'pengajuan_title' => $tracking->pengajuanHki->judul_karya,
                'pengajuan_id' => $tracking->pengajuan_hki_id,
                'created_at' => $tracking->created_at->diffForHumans(),
                'color' => $tracking->color,
                'icon' => $tracking->icon
            ];
        }));
    }

    /**
     * Check permission untuk view tracking
     */
    private function canViewTracking($pengajuan)
    {
        $user = auth()->user();
        
        // Admin dapat akses semua
        if ($user->isAdmin()) {
            return true;
        }
        
        // Pemilik pengajuan dapat akses
        if ($pengajuan->user_id === $user->id) {
            return true;
        }
        
        // Direktur dapat akses untuk approval
        if ($user->isDirektur()) {
            return true;
        }
        
        return false;
    }

    /**
     * Check permission untuk public tracking
     */
    private function canViewPublicTracking($pengajuan, $token)
    {
        // Implementasi token-based access atau nomor pengajuan
        // Untuk sekarang allow untuk demo
        return true;
    }

    /**
     * Check permission untuk update tracking
     */
    private function canUpdateTracking($pengajuan)
    {
        $user = auth()->user();
        
        // Hanya admin dan direktur yang bisa update tracking
        return $user->isAdmin() || $user->isDirektur();
    }
}
