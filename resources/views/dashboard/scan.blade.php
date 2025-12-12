<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Event Ticket Validator</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        #reader {
            border: 4px solid #ddd;
            border-radius: 14px;
            overflow: hidden;
        }
    </style>
</head>

<body class="bg-light">

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">

            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">🎟️ Live QR Code Validator</h4>
                </div>

                <div class="card-body">

                    <p class="text-muted">Point your phone camera at a QR code for instant validation.</p>

                    <div class="text-center mb-3">
                        <div id="reader" style="width:100%; max-width:420px; margin:auto;"></div>
                    </div>

                    <div id="live-status" class="alert alert-info text-center fw-bold">
                        Awaiting Scan...
                    </div>

                    <hr>

                    <!-- Manual Lookup -->
                    <form id="manual-scan-form">
                        <h5 class="text-primary mb-2 fw-bold">Manual Lookup</h5>
                        <div class="input-group mb-2">
                            <input type="text" id="manual-code" class="form-control form-control-lg"
                                   placeholder="Enter Ticket Code" required>
                            <button class="btn btn-secondary btn-lg" type="submit">Check</button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- QR Scanner -->
<script src="https://unpkg.com/html5-qrcode"></script>
<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Sounds -->
<audio id="successSound" src="/sounds/success.mp3" preload="auto"></audio>
<audio id="errorSound" src="/sounds/error.mp3" preload="auto"></audio>
<audio id="usedSound" src="/sounds/used.mp3" preload="auto"></audio>

<script>
let scanner;
let isProcessing = false;

const successSound = document.getElementById("successSound");
const errorSound   = document.getElementById("errorSound");
const usedSound    = document.getElementById("usedSound");
const liveStatus   = document.getElementById("live-status");
const inputManual  = document.getElementById("manual-code");
const manualForm   = document.getElementById("manual-scan-form");

// ===========================================
// MAIN CHECK FUNCTION
// ===========================================
async function checkTicket(code, isManual = false) {

    if (isProcessing || !code) return;
    isProcessing = true;

    liveStatus.className = "alert alert-warning text-center fw-bold";
    liveStatus.innerText = isManual ? "Checking manually..." : "Processing scan...";

    try {
        // SEND COOKIES (VERY IMPORTANT)

        const res = await fetch(`{{ route('admin.scan.check') }}?code=${encodeURIComponent(code)}`, {
            credentials: "include",
            redirect: "manual"
        });

        if (res.type === "opaqueredirect") {
            throw new Error("Authentication required — please login again.");
        }

        if (!res.ok) throw new Error(`HTTP error ${res.status}`);

        const raw = await res.text();
        let data;

        // JSON VALIDATION
        try {
            data = JSON.parse(raw);
        } catch {
            throw new Error("Invalid JSON received from server");
        }

        let icon = "error";
        let sound = errorSound;
        let confirmColor = "#d33";

        if (data.title === "Used Ticket") {
            icon = "warning";
            sound = usedSound;
            confirmColor = "#ffc107";
        }

        if (data.status === "success") {
            icon = "success";
            sound = successSound;
            confirmColor = "#198754";
        }

        sound.play();

        if (!isManual && scanner) await scanner.clear();

        await Swal.fire({
            title: data.title,
            html: `
                <p>${data.message}</p>
                <p><b>Ticket:</b> ${code}</p>
                ${data.used_at ? `<p><b>Used At:</b> ${data.used_at}</p>` : ""}
            `,
            icon,
            confirmButtonText: "Scan Again",
            confirmButtonColor: confirmColor,
            allowOutsideClick: false
        });

        // RESET UI
        isProcessing = false;
        liveStatus.className = "alert alert-info text-center fw-bold";
        liveStatus.innerText = "Awaiting Scan...";
        inputManual.value = "";

        if (!isManual && scanner) {
           scanner.render(onScanSuccess);
        }

    } catch (e) {
        console.error("CHECK ERROR:", e);
        errorSound.play();

        isProcessing = false;
        liveStatus.className = "alert alert-danger text-center fw-bold";
        liveStatus.innerText = `server/network error! ${e.message}`;
    }
}

// ===========================================
// QR SCAN CALLBACK
// ===========================================
function onScanSuccess(decodedText) {
    if (!isProcessing) checkTicket(decodedText);
}

// ===========================================
// INIT CAMERA
// ===========================================
function startScanner() {
    scanner = new Html5QrcodeScanner("reader", {
        fps: 10,
        qrbox: 260,
        rememberLastUsedCamera: true
    });

    scanner.render(onScanSuccess);
}

// ===========================================
// MANUAL CHECK SUBMISSION
// ===========================================
manualForm.addEventListener("submit", (e) => {
    e.preventDefault();
    checkTicket(inputManual.value.trim(), true);
});

// ===========================================
window.addEventListener("load", startScanner);
</script>

</body>
</html>
