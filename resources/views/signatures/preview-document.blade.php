@extends('layouts.app')

@section('content')
<x-page-header 
    title="Preview Dokumen Bertanda Tangan" 
    description="Pratinjau surat pengalihan & pernyataan yang telah dibubuhi tanda tangan digital" 
    icon="fas fa-file-pdf" 
    :breadcrumbs="[
        ['title' => 'Pengajuan', 'url' => route('pengajuan.index')],
        ['title' => 'Kelola Tanda Tangan', 'url' => route('signatures.index', $pengajuan->id)],
        ['title' => 'Preview Dokumen']
    ]"/>

@php

$docs = is_string($pengajuan->file_dokumen_pendukung)
    ? json_decode($pengajuan->file_dokumen_pendukung, true)
    : ($pengajuan->file_dokumen_pendukung ?? []);
$suratPengalihan = $docs['signed']['surat_pengalihan'] ?? ($docs['surat_pengalihan'] ?? null);
$suratPernyataan = $docs['signed']['surat_pernyataan'] ?? ($docs['surat_pernyataan'] ?? null);
$formPermohonan = $docs['signed']['form_permohonan_pendaftaran'] ?? ($docs['form_permohonan_pendaftaran'] ?? null);
$ktpGabungan = $docs['ktp_gabungan'] ?? null;
$items = [
    'surat_pengalihan' => $suratPengalihan,
    'surat_pernyataan' => $suratPernyataan,
    'form_permohonan_pendaftaran' => $formPermohonan,
];
$items['ktp_gabungan'] = $ktpGabungan;

$showKtpGabungan = false;
if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isDirektur())) {
    $showKtpGabungan = true;
}
@endphp

<div class="container-fluid py-4">
    <div class="row">
        @foreach($items as $label => $path)
            @if($label === 'ktp_gabungan' && !$showKtpGabungan)
                @continue
            @endif
        <div class="col-12 mb-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold text-capitalize">
                        <i class="fas fa-file-pdf me-2"></i>{{ str_replace('_', ' ', $label) }}
                    </h5>
                    @if($path)
                    <a href="{{ Storage::url($path) }}" target="_blank" class="btn btn-sm btn-light">
                        <i class="fas fa-download me-1"></i>Download
                    </a>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($path && Storage::disk('public')->exists($path))
                        <iframe src="{{ Storage::url($path) }}#toolbar=0" style="width:100%; height:800px; border:none;"></iframe>
                    @else
                        <div class="p-5 text-center text-muted">
                            <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                            <h5>Dokumen belum tersedia</h5>
                            <p class="mb-0">Dokumen ini belum di-upload atau belum ditandatangani.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-end">
        <a href="{{ route('signatures.index', $pengajuan->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>
@endsection 