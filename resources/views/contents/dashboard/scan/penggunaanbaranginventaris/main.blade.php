@extends('templates.dashboard.main')

@section('mystyles')
    <style>
        body {
            position: relative;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #eef0f8;
        }
    </style>
@endsection

@section('contents')
    <div class="scanner">
        <div class="card-header bg-light d-flex align-items-center justify-content-center mb-5">
            <a href="/" class="text-decoration-none text-center my-2">
                <h4 class="text-center font-weight-light mb-2 text-success">Roemah 54</h4>
                <span class="text-center font-weight-light text-success">Scan QR Code Penggunaan Barang Inventaris</span>
            </a>
        </div>
        <video id="reader" style="width: 400px;"></video>
    </div>
@endsection

@push('myscripts')
    <script type="text/javascript" src="https://unpkg.com/@zxing/library@latest"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        })

        let selectedDeviceId = null;
        const codeReader = new ZXing.BrowserMultiFormatReader();

        function initScanner() {
            codeReader
                .listVideoInputDevices()
                .then(videoInputDevices => {
                    if (videoInputDevices.length > 0) {
                        // Loop through the devices and select the rear camera
                        for (const device of videoInputDevices) {
                            if (device.label.toLowerCase().includes('back')) {
                                selectedDeviceId = device.deviceId;
                                break;
                            }
                        }

                        if (!selectedDeviceId) {
                            // If no back camera found, use the first available camera
                            selectedDeviceId = videoInputDevices[0].deviceId;
                        }

                        function scanOnce() {
                            codeReader
                                .decodeOnceFromVideoDevice(selectedDeviceId, 'reader')
                                .then(result => {
                                    $.ajax({
                                        url: "{{ route('scan.getscanpenggunaanbaranginventaris') }}",
                                        type: "POST",
                                        data: {
                                            'no_barcode': result.text
                                        },
                                        success: function(response) {
                                            if (response.message == "success") {
                                                $("#universalModalContent").empty();
                                                $("#universalModalContent").addClass(
                                                    "modal-dialog-centered");
                                                $("#universalModalContent").append(`
                                                    <div class="modal-content">
                                                        <div class="modal-body">
                                                            <div class="loading">
                                                                <span class="dots pulse1"></span>
                                                                <span class="dots pulse2"></span>
                                                                <span class="dots pulse3"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                `);

                                                $("#universalModal").modal("show");

                                                setTimeout(function() {
                                                    $("#universalModalContent").html(response
                                                        .dataHTML.trim());
                                                    // Start scanning again
                                                    scanOnce();
                                                }, 1000);
                                            } else {
                                                // Start scanning again after unsuccessful scan
                                                scanOnce();

                                                Swal.fire({
                                                    title: "Gagal",
                                                    text: "Opps, Data tidak ditemukan",
                                                    icon: "error"
                                                });
                                            }
                                        },
                                        error: function() {
                                            // Start scanning again after an AJAX error
                                            scanOnce();

                                            Swal.fire({
                                                title: "Gagal",
                                                text: "Opps, Data tidak ditemukan",
                                                icon: "error"
                                            });
                                        }
                                    });

                                })
                                .catch(err => {
                                    console.error(err);
                                    // Mulai pemindaian lagi setelah pembacaan qrcode error
                                    scanOnce();
                                });
                        }

                        // Memulai pemindaian
                        scanOnce();
                    } else {
                        Swal.fire({
                            title: "Gagal",
                            text: "Kamera tidak ditemukan",
                            icon: "error"
                        })
                    }
                })
                .catch(err => console.error(err));
        }

        if (navigator.mediaDevices) {
            initScanner();
        } else {
            Swal.fire({
                title: "Gagal",
                text: "Kamera tidak ditemukan",
                icon: "error"
            })
        }
    </script>
@endpush
