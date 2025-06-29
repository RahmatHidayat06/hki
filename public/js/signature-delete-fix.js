// PERBAIKAN TOMBOL BUANG SIGNATURE - Standalone JavaScript
// File: public/js/signature-delete-fix.js

$(document).ready(function() {
    // PERBAIKAN: Delete signature from dropdown (hapus permanen dari storage)
    $(document).on('click', '.delete-signature', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Delete signature button clicked');
        
        var button = $(this);
        var signaturePath = button.data('path');
        var signatureItem = button.closest('.dropdown-item, .signature-quick-item');
        var signatureName = signatureItem.find('.fw-semibold').text() || 'Tanda Tangan';
        
        if (!signaturePath) {
            Swal.fire({
                title: 'Error!',
                text: 'Path tanda tangan tidak ditemukan.',
                icon: 'error'
            });
            return;
        }
        
        // Konfirmasi sebelum menghapus permanen
        Swal.fire({
            title: 'Hapus Tanda Tangan Permanen?',
            html: '<div class="text-start">' +
                '<p><strong>Tanda Tangan:</strong> ' + signatureName + '</p>' +
                '<p><strong>Path:</strong> <code>' + signaturePath + '</code></p>' +
                '<div class="alert alert-warning mt-3">' +
                    '<i class="fas fa-exclamation-triangle me-2"></i>' +
                    '<strong>Peringatan:</strong> Tanda tangan akan dihapus permanen dari storage dan tidak dapat dipulihkan!' +
                '</div>' +
            '</div>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus Permanen!',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (result.isConfirmed) {
                // Show loading
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                
                // AJAX request untuk hapus dari storage
                $.ajax({
                    url: '/signature/delete',
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        path: signaturePath
                    },
                    success: function(response) {
                        // Hapus dari dropdown UI
                        signatureItem.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Cek apakah masih ada signature tersisa
                            if ($('.dropdown-item[data-type="signature"]').length === 0) {
                                $('.quick-signatures').hide();
                                $('#signatureDropdown').html('<i class="fas fa-signature me-2"></i>Tidak ada tanda tangan tersedia');
                            } else {
                                // Update counter
                                var remainingCount = $('.dropdown-item[data-type="signature"]').length;
                                $('#signatureDropdown').html('<i class="fas fa-signature me-2"></i>Pilih Tanda Tangan (' + remainingCount + ' tersedia)');
                            }
                        });
                        
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message || 'Tanda tangan berhasil dihapus dari storage.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        // Restore button
                        button.prop('disabled', false).html('<i class="fas fa-trash"></i>');
                        
                        var errorMessage = 'Gagal menghapus tanda tangan.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });

    // Prevent delete button from triggering parent click handlers
    $(document).on('click', '.draggable-item', function(e) {
        // Ignore if click was on internal .use-signature button
        if ($(e.target).closest('.use-signature').length) return;
        // Ignore if click was on .delete-signature button
        if ($(e.target).closest('.delete-signature').length) return;
        
        // Continue with original draggable item click handling
        var originalHandler = window.originalDraggableItemClick;
        if (typeof originalHandler === 'function') {
            originalHandler.call(this, e);
        }
    });
});

// Enhanced signature styling with padding
function enhanceSignatureDisplay() {
    // Add better styling for signature overlays
    var css = `
        .placed-item[data-type="signature"] {
            padding: 5px !important;
            background: rgba(255,255,255,0.9) !important;
            border: 1px solid #ddd !important;
            border-radius: 3px !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
        }
        
        .placed-item[data-type="signature"] img {
            max-width: 100% !important;
            max-height: 100% !important;
            object-fit: contain !important;
        }
        
        .signature-item {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .signature-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .delete-signature {
            transition: background-color 0.2s ease, transform 0.2s ease;
        }
        
        .delete-signature:hover {
            transform: scale(1.1);
            background-color: #dc3545 !important;
            color: white !important;
        }
    `;
    
    // Inject CSS
    if (!document.getElementById('signature-enhancement-css')) {
        var style = document.createElement('style');
        style.id = 'signature-enhancement-css';
        style.textContent = css;
        document.head.appendChild(style);
    }
}

// Initialize enhancement when document is ready
$(document).ready(function() {
    enhanceSignatureDisplay();
}); 