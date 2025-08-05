@extends('layouts.master')

@section('title', 'Ticket QR Scanner')

@section('content')
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Scan Ticket QR Code</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="p-4">
                        <p class="text-muted mb-4">Point your device's camera at the ticket's QR code to scan and redeem.</p>

                        <div id="qr-reader" style="width: 100%;"></div>
                        <div id="qr-reader-results" class="mt-4 text-center"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Include the html5-qrcode library --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const qrReaderResultsDiv = document.getElementById('qr-reader-results');
            const html5QrCode = new Html5Qrcode("qr-reader");

            // Function to get the back camera if available
            function getBackCamera(devices) {
                const backCamera = devices.find(device =>
                    device.label.toLowerCase().includes('back') ||
                    device.label.toLowerCase().includes('rear')
                );
                return backCamera ? backCamera.id : devices[0].id; // Use back camera or default to the first
            }

            // Scan success callback function
            const onScanSuccess = (decodedText, decodedResult) => {
                // Stop the scanner to prevent multiple scans
                html5QrCode.stop().then(() => {
                    qrReaderResultsDiv.innerHTML = '<div class="alert alert-info">Processing ticket...</div>';

                    // Send the decoded URL to the server for redemption
                    fetch(decodedText)
                        .then(response => {
                            // Redirect to the status page returned by the server
                            window.location.href = response.url;
                        })
                        .catch(error => {
                            console.error('Error during redemption fetch:', error);
                            qrReaderResultsDiv.innerHTML =
                                `<div class="alert alert-danger">Error: Could not connect to the server.</div>`;
                        });
                }).catch(err => {
                    console.error('Error stopping the scanner:', err);
                });
            };

            const onScanFailure = (error) => {
                // This is called constantly, so we won't log or display non-critical errors.
                // We'll only handle the initial camera access error.
            };

            // Start scanning with robust error handling
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    const cameraId = getBackCamera(devices);
                    html5QrCode.start(
                        cameraId, {
                            fps: 10,
                            qrbox: { width: 250, height: 250 }
                        },
                        onScanSuccess,
                        onScanFailure
                    ).catch(err => {
                        console.error('Error starting scanner:', err);
                        qrReaderResultsDiv.innerHTML =
                            `<div class="alert alert-danger">An error occurred while starting the camera. Please check your browser's permissions.</div>`;
                    });
                } else {
                    qrReaderResultsDiv.innerHTML =
                        `<div class="alert alert-warning">No camera found on this device.</div>`;
                }
            }).catch(err => {
                console.error('Error getting camera access:', err);
                if (window.location.protocol === 'http:') {
                    // This is the specific error you encountered.
                    qrReaderResultsDiv.innerHTML =
                        `<div class="alert alert-danger">
                        <strong>Camera access denied!</strong><br>
                        Your browser is blocking camera access because you are using an insecure connection (http).
                        Please access the app via <strong>https://</strong> or on <strong>localhost</strong> during development.
                    </div>`;
                } else {
                    qrReaderResultsDiv.innerHTML =
                        `<div class="alert alert-danger">Error getting camera access: ${err}</div>`;
                }
            });
        });
    </script>
@endsection
