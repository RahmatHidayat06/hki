
// Optimized PDF loading test
function testPDFLoading() {
    const pdfUrl = '/storage/dokumen_pengalihan/sRrL48qFOLnSFBWvgvxI7h4eoWOFaaCDnxi5ZyRB.pdf';
    console.log('Testing PDF URL:', pdfUrl);
    
    // Test 1: Basic fetch
    fetch(pdfUrl, { method: 'HEAD' })
        .then(response => {
            console.log('✅ PDF accessible:', response.status);
            return fetch(pdfUrl);
        })
        .then(response => response.blob())
        .then(blob => {
            console.log('✅ PDF downloaded:', blob.size, 'bytes');
            console.log('✅ PDF type:', blob.type);
            
            // Test PDF.js loading
            if (typeof pdfjsLib !== 'undefined') {
                const fileReader = new FileReader();
                fileReader.onload = function() {
                    const typedarray = new Uint8Array(this.result);
                    pdfjsLib.getDocument(typedarray).promise.then(pdf => {
                        console.log('✅ PDF.js loaded successfully:', pdf.numPages, 'pages');
                    }).catch(error => {
                        console.error('❌ PDF.js error:', error);
                    });
                };
                fileReader.readAsArrayBuffer(blob);
            }
        })
        .catch(error => {
            console.error('❌ PDF loading failed:', error);
        });
}

// Auto-run test
testPDFLoading();
