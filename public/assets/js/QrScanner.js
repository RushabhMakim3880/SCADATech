let qrScanner = null;
let qrModal = null;
let lastScannedCode = null;
let lastScannedTime = 0;


$(document).on('click', '.scanQR', async function () {

    if (isIos() && isStandalone) {
        mtplAlerts.show('error', 'Camera access is blocked inside iOS PWA. Please open this in Safari browser.', "Error");
        return;
    }



    const continueScan = $(this).data('continue') === 1;

    try {
        const cameras = await Html5Qrcode.getCameras();
        if (!cameras || cameras.length === 0) {
            mtplAlerts.show('error', 'No camera found on this device.', "Error");
            return;
        }
    } catch (err) {
        mtplAlerts.show('error', 'Camera access denied or not available.', "Error");
        return;
    }

    // Remove and recreate modal HTML (to fix camera re-init bug)
    $('#qrScanModal').remove();
    $('body').append(`
        <div class="modal fade" id="qrScanModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3">
              <div class="modal-header">
                <h5 class="modal-title">Scan QR / Barcode</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body text-center">
                <div id="qr-reader" style="width: 100%; max-width: 350px; margin: auto;"></div>
              </div>
            </div>
          </div>
        </div>
    `);

    qrModal = new bootstrap.Modal(document.getElementById('qrScanModal'));
    qrModal.show();

    // Rebind close event for cleanup
    $('#qrScanModal').on('hidden.bs.modal', function () {
        if (qrScanner) {
            qrScanner.stop().then(() => qrScanner.clear());
            qrScanner = null;
        }
    });

    setTimeout(() => {
        qrScanner = new Html5Qrcode("qr-reader");

        qrScanner.start(
            { facingMode: "environment" },
            {
                fps: 10,
                qrbox: { width: 300, height: 150 },
                aspectRatio: 1.75 // better for barcode orientation
            },
            (decodedText) => {
                const now = Date.now();

                // Ignore repeated scans within 2 seconds
                if (decodedText === lastScannedCode && (now - lastScannedTime) < 2000) {
                    return;
                }

                lastScannedCode = decodedText;
                lastScannedTime = now;

                console.log("Scanned:", decodedText);
                $(document).trigger("qr-scanned", [decodedText]);

                if (!continueScan) {
                    qrScanner.stop().then(() => {
                        qrScanner.clear();
                        qrModal.hide();
                    });
                }
            },
            (err) => {
                // ignore scan errors
            }
        );
    }, 500);
});


$(document).on('qr-scanned', function (e, code) {
    console.log("Scanned Code:", code);
    mtplAlerts.show('success', 'QR Code Scanned: ' + code, "Success");
});