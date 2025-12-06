<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Buy Ticket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">


<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">

            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">QR Code Validator</h4>
                </div>

                <div class="card-body">

                    <p class="text-muted mb-3">
                        Point your camera at a ticket’s QR code to validate it.
                    </p>

                    <div class="d-flex justify-content-center mb-4">
                        <div id="reader" style="width:320px;"></div>
                    </div>

                    <div id="result"></div>

                </div>
            </div>

        </div>
    </div>

</div>

{{-- QR Scanner --}}
<script src="https://unpkg.com/html5-qrcode"></script>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- SOUND EFFECTS --}}
<audio id="successSound">
    <source src="/sounds/success.mp3" type="audio/mpeg">
</audio>

<audio id="errorSound">
    <source src="/sounds/error.mp3" type="audio/mpeg">
</audio>

<script>
let scannerInstance;
let scanStopped = false;

function onScanSuccess(decodedText) {

    if (scanStopped) return;
    scanStopped = true;

    fetch("/admin/scan/check?code=" + decodedText)
        .then(res => res.json())
        .then(data => {

            // Play sound
            if (data.status === "success") {
                document.getElementById("successSound").play();
            } else {
                document.getElementById("errorSound").play();
            }

            // Auto-stop scanner
            scannerInstance.clear();

            // SWEETALERT POPUP
            Swal.fire({
                title: data.status === "success" ? "Valid Ticket!" : "Invalid Ticket!",
                html: `
                    <p style="font-size:16px">${data.message}</p>
                    <p><b>Ticket Code:</b> ${decodedText}</p>
                `,
                icon: data.status === "success" ? "success" : "error",
                confirmButtonText: "Scan Again",
                confirmButtonColor: data.status === "success" ? "#28a745" : "#d33"
            }).then(() => {
                // Restart scanner
                scanStopped = false;
                scannerInstance.render(onScanSuccess);
            });

        });
}

// Initialize scanner
scannerInstance = new Html5QrcodeScanner("reader", {
    fps: 10,
    qrbox: 250
});

scannerInstance.render(onScanSuccess);
</script>
