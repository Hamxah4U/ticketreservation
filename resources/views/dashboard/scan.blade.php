{{-- @extends('layouts.admin')

@section('content') --}}
<h2 class="mb-4">QR Code Validator</h2>

<div id="reader" style="width:300px"></div>

<div id="result" class="mt-4"></div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
function onScanSuccess(decodedText) {
    fetch("/admin/scan/check?code=" + decodedText)
        .then(res => res.json())
        .then(data => {
            document.getElementById('result').innerHTML = `
                <div class="alert alert-${data.status}">
                    <h4>${data.message}</h4>
                    <p><b>Ticket Code:</b> ${decodedText}</p>
                </div>
            `;
        });
}

new Html5QrcodeScanner("reader", {
    fps: 10,
    qrbox: 200
}).render(onScanSuccess);
</script>


{{-- @endsection --}}
