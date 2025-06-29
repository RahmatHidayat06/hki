// Digital Signature Pad & Upload Handler
// File: public/js/signature-digital.js
// Requires: signature_pad.umd.min.js, jQuery, SweetAlert2

(function ($) {
    'use strict';

    let signaturePad;

    function initSignaturePad() {
        const canvas = document.getElementById('signaturePad');
        if (!canvas) {
            return; // no canvas present on this page
        }
        signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255,255,255,0)',
            penColor: '#000'
        });
        // Resize canvas to device pixel ratio for crisp lines
        resizeCanvas(canvas, signaturePad);
        window.addEventListener('resize', () => resizeCanvas(canvas, signaturePad));
    }

    function resizeCanvas(canvas, pad) {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext('2d').scale(ratio, ratio);
        pad && pad.clear();
    }

    // Clear button
    window.clearSignature = function () {
        if (signaturePad) signaturePad.clear();
    };

    // Save Drawn Signature
    function handleSaveDrawnSignature() {
        if (!signaturePad || signaturePad.isEmpty()) {
            Swal.fire({
                icon: 'warning',
                title: 'Tanda tangan kosong',
                text: 'Silakan gambar tanda tangan terlebih dahulu.'
            });
            return;
        }
        const dataUrl = signaturePad.toDataURL('image/png');

        $.ajax({
            url: '/signature/save',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                signature_data: dataUrl,
                signature_method: 'draw'
            },
            beforeSend() {
                $('#saveSignatureBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            },
            success(resp) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: resp.message || 'Tanda tangan berhasil disimpan',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error(xhr) {
                $('#saveSignatureBtn').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Simpan TTD');
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: xhr.responseJSON?.message || 'Gagal menyimpan tanda tangan.'
                });
            }
        });
    }

    // Upload preview handler
    function initUploadPreview() {
        const fileInput = $('#signatureFile');
        if (!fileInput.length) return;

        fileInput.on('change', function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                $('#signaturePreview').show();
                $('#signatureImage').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        });
    }

    window.removeSignatureFile = function () {
        $('#signatureFile').val('');
        $('#signaturePreview').hide();
    };

    // Save uploaded signature
    function handleSaveUploadedSignature() {
        const file = document.getElementById('signatureFile').files[0];
        if (!file) {
            Swal.fire({ icon: 'warning', title: 'File belum dipilih' });
            return;
        }
        const formData = new FormData();
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('signature_file', file);

        $.ajax({
            url: '/signature/save',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend() {
                $('#saveUploadBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            },
            success(resp) {
                Swal.fire({ icon: 'success', title: 'Berhasil', text: resp.message, timer: 1500, showConfirmButton: false })
                    .then(() => location.reload());
            },
            error(xhr) {
                $('#saveUploadBtn').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Simpan TTD Upload');
                Swal.fire({ icon: 'error', title: 'Gagal', text: xhr.responseJSON?.message || 'Gagal upload tanda tangan.' });
            }
        });
    }

    // Bind buttons on DOM ready
    $(function () {
        initSignaturePad();
        initUploadPreview();

        $('#saveSignatureBtn').on('click', handleSaveDrawnSignature);
        $('#saveUploadBtn').on('click', handleSaveUploadedSignature);
    });

})(jQuery); 