@extends('layouts.app')

@section('content')
<div class="container py-5 fade-in">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-5">
                    <h2 class="h4 fw-bold text-center mb-4">Pembayaran Pengajuan HKI</h2>

                    <!-- Billing Code -->
                    <div class="text-center mb-4">
                        <p class="mb-1 text-muted">Gunakan kode billing berikut untuk melakukan pembayaran:</p>
                        <div class="display-6 fw-bold text-primary d-inline-block bg-light px-4 py-2 rounded" id="billingCode">{{ $pengajuan->billing_code }}</div>
                        <button class="btn btn-outline-secondary btn-sm ms-2" onclick="copyBilling()"><i class="fas fa-copy"></i></button>
                        <small class="d-block mt-1 text-success" id="copyMsg" style="display:none;">Disalin!</small>
                    </div>

                    <!-- Payment Steps -->
                    <h5 class="fw-semibold mb-3">Langkah Pembayaran</h5>
                    <ol class="list-group list-group-numbered mb-4">
                        <li class="list-group-item">Salin kode billing di atas.</li>
                        <li class="list-group-item">Masuk ke mobile banking atau gerai bank/ATM yang mendukung.</li>
                        <li class="list-group-item">Pilih menu <strong>Tagihan</strong> &gt; <strong>Pembayaran Penerimaan Negara (MPN)</strong>.</li>
                        <li class="list-group-item">Tempelkan kode billing, konfirmasi, dan selesaikan pembayaran.</li>
                        <li class="list-group-item">Setelah berhasil, simpan bukti pembayaran (PDF / JPG).</li>
                    </ol>

                    <!-- Upload Proof Inline -->
                    <hr class="my-4">
                    <h5 class="fw-semibold mb-3">Unggah Bukti Pembayaran</h5>
                    <form id="uploadForm" action="{{ route('pembayaran.submit', $pengajuan->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <input type="file" name="bukti" id="buktiInput" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>
                        <div id="previewContainer" class="mb-3" style="display:none;"></div>
                        <div class="text-end">
                            <button type="submit" id="submitBtn" class="btn btn-primary mt-2"><i class="fas fa-upload me-1"></i>Kirim Bukti</button>
                        </div>
                    </form>
                    <p class="small text-muted mb-0 text-center">Setelah bukti terkirim, status berubah menjadi <strong>menunggu verifikasi pembayaran</strong>.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyBilling(){
    const code = document.getElementById('billingCode').innerText.trim();
    navigator.clipboard.writeText(code).then(()=>{
        const msg = document.getElementById('copyMsg');
        msg.style.display = 'inline';
        setTimeout(()=>msg.style.display='none', 2000);
    });
}

document.addEventListener('DOMContentLoaded', function(){
    const input = document.getElementById('buktiInput');
    if(!input) return;
    const preview = document.getElementById('previewContainer');
    const form = document.getElementById('uploadForm');

    input.addEventListener('change', function(){
        const file = this.files && this.files[0];
        if(!file) return;

        // clear preview
        preview.innerHTML = '';
        const url = URL.createObjectURL(file);
        const ext = file.name.split('.').pop().toLowerCase();
        if(['jpg','jpeg','png','gif'].includes(ext)){
            const img = document.createElement('img');
            img.src = url;
            img.alt = 'Preview';
            img.className = 'img-fluid rounded shadow';
            preview.appendChild(img);
        }else if(ext === 'pdf'){
            const embed = document.createElement('embed');
            embed.src = url;
            embed.type = 'application/pdf';
            embed.width = '100%';
            embed.height = '400';
            preview.appendChild(embed);
        }else{
            preview.textContent = 'Pratinjau tidak tersedia.';
        }

        preview.style.display = 'block';
    });
});
</script>
@endpush 