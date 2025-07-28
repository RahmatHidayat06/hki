@extends('layouts.app')

@section('title', 'Tanda Tangan Sudah Dikirim')

@section('content')
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
            <i class="fas fa-check-circle fa-4x text-success mb-4"></i>
            <h2 class="fw-bold mb-3">Terima kasih!</h2>
            <p class="lead">Tanda tangan Anda telah berhasil disimpan.</p>

            <div class="mt-4">
                <a href="{{ route('signatures.preview-document', $pengajuan->id) }}" class="btn btn-primary me-2">
                    <i class="fas fa-eye me-1"></i>Lihat Dokumen Bertanda Tangan
                </a>
                <a href="{{ route('signatures.progress', $pengajuan->id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-list me-1"></i>Lihat Progress Tanda Tangan
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 