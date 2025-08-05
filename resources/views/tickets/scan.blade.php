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

                        <div id="scanner-container">
                            <video id="scanner-video" class="w-100" style="border: 1px solid #ddd;"></video>
                        </div>
                        <div id="qr-reader-results" class="mt-4 text-center"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
{{--    <script type="module">--}}
{{--        import {--}}
{{--            BrowserQRCodeReader,--}}
{{--            BrowserCodeReader--}}
{{--        } from 'https://cdn.jsdelivr.net/npm/@zxing/browser@0.0.9/+esm';--}}

{{--        document.addEventListener('DOMContentLoaded', async function () {--}}
{{--            const qrReaderResultsDiv = document.getElementById('qr-reader-results');--}}
{{--            const videoElement = document.getElementById('scanner-video');--}}
{{--            const codeReader = new BrowserQRCodeReader();--}}

{{--            // Get back camera if available--}}
{{--            async function getBackCamera(devices) {--}}
{{--                const backCamera = devices.find(device =>--}}
{{--                    device.label.toLowerCase().includes('back') ||--}}
{{--                    device.label.toLowerCase().includes('rear')--}}
{{--                );--}}
{{--                return backCamera ? backCamera.deviceId : devices[0].deviceId;--}}
{{--            }--}}

{{--            function onScanSuccess(decodedText) {--}}
{{--                qrReaderResultsDiv.innerHTML = '<div class="alert alert-info">Processing ticket...</div>';--}}
{{--                codeReader.reset();--}}

{{--                fetch(decodedText)--}}
{{--                    .then(response => {--}}
{{--                        window.location.href = response.url;--}}
{{--                    })--}}
{{--                    .catch(error => {--}}
{{--                        console.error('Error during redemption fetch:', error);--}}
{{--                        qrReaderResultsDiv.innerHTML =--}}
{{--                            `<div class="alert alert-danger">Error: Could not connect to the server.</div>`;--}}
{{--                    });--}}
{{--            }--}}

{{--            try {--}}
{{--                const videoInputDevices = await BrowserCodeReader.listVideoInputDevices();--}}

{{--                if (videoInputDevices.length) {--}}
{{--                    const cameraId = await getBackCamera(videoInputDevices);--}}

{{--                    codeReader.decodeFromVideoDevice(cameraId, videoElement, (result, err) => {--}}
{{--                        if (result) {--}}
{{--                            onScanSuccess(result.getText());--}}
{{--                        }--}}
{{--                    });--}}
{{--                } else {--}}
{{--                    qrReaderResultsDiv.innerHTML =--}}
{{--                        `<div class="alert alert-warning">No camera found on this device.</div>`;--}}
{{--                }--}}
{{--            } catch (err) {--}}
{{--                console.error('Error getting camera access:', err);--}}

{{--                if (err.name === 'NotAllowedError') {--}}
{{--                    qrReaderResultsDiv.innerHTML =--}}
{{--                        `<div class="alert alert-danger">--}}
{{--                        <strong>Camera access denied!</strong><br>--}}
{{--                        Please allow camera access in your browser settings to use the scanner.--}}
{{--                    </div>`;--}}
{{--                } else if (err.name === 'NotFoundError') {--}}
{{--                    qrReaderResultsDiv.innerHTML =--}}
{{--                        `<div class="alert alert-warning">No camera found on this device.</div>`;--}}
{{--                } else if (window.location.protocol === 'http:' && (err.name === 'NotAllowedError' || err.name === 'SecurityError')) {--}}
{{--                    qrReaderResultsDiv.innerHTML =--}}
{{--                        `<div class="alert alert-danger">--}}
{{--                        <strong>Camera access blocked!</strong><br>--}}
{{--                        Your browser is blocking camera access because you are using an insecure connection (http).--}}
{{--                        Please access the app via <strong>https://</strong> or on <strong>localhost</strong> during development.--}}
{{--                    </div>`;--}}
{{--                } else {--}}
{{--                    qrReaderResultsDiv.innerHTML =--}}
{{--                        `<div class="alert alert-danger">An unexpected error occurred: ${err.message}.</div>`;--}}
{{--                }--}}
{{--            }--}}
{{--        });--}}
{{--    </script>--}}
<script type="module">
    import {
        BrowserQRCodeReader,
        BrowserCodeReader
    } from 'https://cdn.jsdelivr.net/npm/@zxing/browser@0.0.9/+esm';

    document.addEventListener('DOMContentLoaded', async () => {
        const qrReaderResultsDiv = document.getElementById('qr-reader-results');
        const videoElement = document.getElementById('scanner-video');
        const codeReader = new BrowserQRCodeReader();

        async function getBackCamera(devices) {
            const backCamera = devices.find(device =>
                device.label.toLowerCase().includes('back') ||
                device.label.toLowerCase().includes('rear')
            );
            return backCamera ? backCamera.deviceId : devices[0].deviceId;
        }

        function onScanSuccess(decodedText) {
            qrReaderResultsDiv.innerHTML = '<div class="alert alert-info">Processing ticket...</div>';
            codeReader.reset(); // Stop scanner after success
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

        try {
            const devices = await BrowserCodeReader.listVideoInputDevices();
            console.log('devices: ', devices);

            if (!devices.length) {
                qrReaderResultsDiv.innerHTML =
                    `<div class="alert alert-warning">No camera devices found.</div>`;
                return;
            }

            const cameraId = await getBackCamera(devices);

            console.log('cameraId: ', cameraId);
            codeReader.decodeFromVideoDevice(cameraId, videoElement, (result, error) => {
                if (result) {
                    onScanSuccess(result.getText());
                }
                // Don't log error every frame — too noisy
            });

        } catch (err) {
            console.error('Camera setup error:', err);

            if (err.name === 'NotAllowedError') {
                qrReaderResultsDiv.innerHTML =
                    `<div class="alert alert-danger">
                        <strong>Camera access denied!</strong> Please enable it in your browser settings.
                    </div>`;
            } else if (window.location.protocol === 'http:') {
                qrReaderResultsDiv.innerHTML =
                    `<div class="alert alert-danger">
                        Camera access requires HTTPS or localhost. Please switch to a secure connection.
                    </div>`;
            } else {
                qrReaderResultsDiv.innerHTML =
                    `<div class="alert alert-danger">Error: ${err.message}</div>`;
            }
        }
    });
</script>

@endpush
