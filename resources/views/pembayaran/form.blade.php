@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-info text-white border-0">
                    <h5 class="mb-0 fw-semibold"><i class="fas fa-wallet me-2"></i> Pembayaran Pengajuan HKI</h5>
                </div>
                <div class="card-body p-4">
                    <p>Silakan transfer biaya pendaftaran HKI ke rekening berikut, kemudian unggah bukti pembayaran.</p>
                    <ul class="list-group mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Bank</span><strong>BRI</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Nomor Rekening</span><strong>1234-01-000999-53-9</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Atas Nama</span><strong>Universitas XYZ</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Jumlah</span><strong>Rp150.000</strong>
                        </li>
                    </ul>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="uploadForm" action="{{ route('pembayaran.submit', $pengajuan->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Unggah Bukti Pembayaran (jpg, png, pdf)</label>
                            <input type="file" name="bukti" id="buktiInput" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>

                        <!-- Preview -->
                        <div id="previewContainer" class="mb-3" style="display:none;"></div>

                        <div class="text-end">
                            <button type="submit" id="submitBtn" class="btn btn-primary mt-2"><i class="fas fa-upload me-2"></i>Kirim Bukti</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const input = document.getElementById('buktiInput');
    const preview = document.getElementById('previewContainer');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('uploadForm');

    input.addEventListener('change', function(){
        const file = this.files && this.files[0];
        if(!file) return;

        // Reset preview
        preview.innerHTML = '';

        const ext = file.name.split('.').pop().toLowerCase();
        const url = URL.createObjectURL(file);

        if(['jpg','jpeg','png','gif'].includes(ext)){
            const img = document.createElement('img');
            img.src = url;
            img.alt = 'Preview';
            img.className = 'img-fluid rounded shadow';
            preview.appendChild(img);
        } else if(ext === 'pdf') {
            const embed = document.createElement('embed');
            embed.src = url;
            embed.type = 'application/pdf';
            embed.width = '100%';
            embed.height = '400';
            embed.className = 'shadow';
            preview.appendChild(embed);
        } else {
            preview.innerHTML = '<p class="text-muted">Pratinjau tidak tersedia untuk tipe berkas ini.</p>';
        }

        preview.style.display = 'block';
    });
});
</script>
@endpush 