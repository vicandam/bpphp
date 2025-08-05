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

                        {{-- Container for the QR scanner's video feed --}}
                        <div class="position-relative w-100" style="padding-top: 75%; overflow: hidden;">
                            <video id="qr-video" class="position-absolute w-100 h-100 top-0 left-0" style="object-fit: cover;"></video>
                        </div>

                        {{-- Display area for scan results or errors --}}
                        <div id="qr-reader-results" class="mt-4 text-center"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    {{-- Include the qr-scanner library via CDN --}}
    <script type="module">
        console.log('test');

        import QrScanner from "https://cdn.jsdelivr.net/npm/qr-scanner@1.4.2/qr-scanner.min.js";

        document.addEventListener('DOMContentLoaded', async function () {
            const video = document.getElementById('qr-video');
            const qrReaderResultsDiv = document.getElementById('qr-reader-results');
            let scanner;

            // Function to handle the scanned result
            function onScanSuccess(decodedText) {
                scanner.stop();
                qrReaderResultsDiv.innerHTML = '<div class="alert alert-info">Processing ticket...</div>';

                // Fetch the decoded URL to trigger the redemption process
                fetch(decodedText)
                    .then(response => {
                        window.location.href = response.url;
                    })
                    .catch(error => {
                        console.error('Error during redemption fetch:', error);
                        qrReaderResultsDiv.innerHTML =
                            `<div class="alert alert-danger">Error: Could not connect to the server.</div>`;
                    });
            }

            // Function to handle camera access errors
            function handleCameraError(err) {
                console.error('Camera access error:', err);

                if (err instanceof DOMException && err.name === 'NotAllowedError') {
                    // The user denied camera access
                    qrReaderResultsDiv.innerHTML =
                        `<div class="alert alert-danger">
                        <strong>Camera access denied!</strong><br>
                        Please allow camera access in your browser settings to use the scanner.
                    </div>`;
                } else if (err instanceof DOMException && err.name === 'NotFoundError') {
                    // No camera found on the device
                    qrReaderResultsDiv.innerHTML =
                        `<div class="alert alert-warning">No camera found on this device.</div>`;
                } else if (window.location.protocol === 'http:') {
                    // The specific error for insecure context
                    qrReaderResultsDiv.innerHTML =
                        `<div class="alert alert-danger">
                        <strong>Camera access blocked!</strong><br>
                        Your browser is blocking camera access because you are using an insecure connection (http).
                        Please access the app via <strong>https://</strong> or on <strong>localhost</strong> during development.
                    </div>`;
                } else {
                    // A general error occurred
                    qrReaderResultsDiv.innerHTML =
                        `<div class="alert alert-danger">An unexpected error occurred: ${err.message}.</div>`;
                }
            }

            try {
                // Check for available cameras and prefer the back camera
                const cameras = await QrScanner.listCameras(true);
                const backCamera = cameras.find(camera => camera.label.toLowerCase().includes('back') || camera.label.toLowerCase().includes('rear'));
                const cameraId = backCamera ? backCamera.id : (cameras.length > 0 ? cameras[0].id : null);

                if (cameraId) {
                    // Initialize and start the scanner with the selected camera
                    scanner = new QrScanner(video, onScanSuccess, {
                        onDecodeError: (err) => { /* console.warn(err) */ },
                        highlightScanRegion: true,
                        highlightCodeOutline: true,
                        preferredCamera: cameraId,
                    });

                    await scanner.start();
                } else {
                    qrReaderResultsDiv.innerHTML =
                        `<div class="alert alert-warning">No camera found on this device.</div>`;
                }
            } catch (err) {
                handleCameraError(err);
            }
        });
    </script>
@endpush
