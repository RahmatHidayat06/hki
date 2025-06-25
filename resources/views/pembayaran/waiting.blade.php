@extends('layouts.app')

@section('content')
<div class="container py-5 fade-in">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
            <div class="alert alert-info text-center shadow-sm p-5 rounded-4">
                <h4 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Menunggu Kode Billing</h4>
                <p class="mb-4">Kode billing untuk pembayaran pengajuan HKI Anda belum tersedia. Silakan menunggu pemberitahuan dari admin. Anda akan menerima notifikasi di sistem dan WhatsApp begitu kode billing telah dibuat.</p>
                <a href="{{ route('pengajuan.show', $pengajuan->id) }}" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Kembali ke Detail Pengajuan</a>
            </div>
        </div>
    </div>
</div>
@endsection 