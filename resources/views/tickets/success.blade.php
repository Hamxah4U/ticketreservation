<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Purchased Successfully</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        body { background: #f5f7fa; }
        .ticket-card {
            max-width: 450px;
            margin: 40px auto;
            padding: 25px;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 6px 22px rgba(0,0,0,0.1);
        }
        .qr-box {
            padding: 12px;
            background: #f1f1f1;
            border-radius: 10px;
            display: flex;
            justify-content: center;
        }
        .download-btns {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>

<div class="ticket-card text-center">

    <h3 class="mb-1 text-success fw-bold">Payment Successful for 🎉</h3>
    <h3 class="mb-0">Arabian Festival Bauchi</h3>

    <!-- New polished message -->
    <p class="text-muted">
        Hello <strong>{{ $ticket->buyer_name }}</strong>,<br><br>
        Thank you for your purchase! Your event ticket is ready.
        Please present the QR code below at the event entrance for verification.<br><br>
        Kindly keep this ticket safe as you will need the QR code to gain entry.
    </p>

    <!-- QR CODE -->
    <div class="qr-box my-3">
        <img src="{{ $dataUri ?? '' }}" width="200" alt="QR Code">
    </div>

    <h4 class="fw-bold mt-3">{{ $ticket->ticket_code }}</h4>

    <hr>

    {{-- <p class="mb-1"><strong>Name:</strong> {{ $ticket->buyer_name }}</p> --}}
    <p class="mb-1"><strong>Email:</strong> {{ $ticket->buyer_email }}</p>
    <p class="mb-1">
        <strong>Amount Paid:</strong> ₦{{ number_format($ticket->price, 2) }}
    </p>

    <p>
        This ticket is for personal use only. Do NOT allow anyone to scan this QR code. If scanned by another person, the ticket will become invalid
    </p>

    <hr>

    <p class="text-muted small">
        A copy of your ticket has also been sent to your email.
    </p>

    <div class="download-btns my-3">
        <button id="downloadPDF" class="btn btn-success btn-lg">Download PDF</button>
        <button id="downloadPNG" class="btn btn-info btn-lg">Download PNG</button>
    </div>

    <a href="{{ url('/buy') }}" class="btn btn-primary btn-lg mt-2">Go Home</a>

</div>

<script>
    function hideElementsBeforeCapture() {
        document.querySelector('.download-btns').style.display = 'none';
        document.querySelector('.btn-primary.mt-2').style.display = 'none';
    }

    function restoreElementsAfterCapture() {
        document.querySelector('.download-btns').style.display = 'flex';
        document.querySelector('.btn-primary.mt-2').style.display = 'inline-block';
    }

    // Download PDF
    document.getElementById('downloadPDF').addEventListener('click', function () {
        const ticketCard = document.querySelector('.ticket-card');

        hideElementsBeforeCapture();  // 🔥 Hide buttons BEFORE capture

        setTimeout(() => {
            html2canvas(ticketCard, { scale: 2 }).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const { jsPDF } = window.jspdf;

                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'px',
                    format: [ticketCard.offsetWidth + 40, ticketCard.offsetHeight + 40]
                });

                pdf.addImage(imgData, 'PNG', 20, 20, ticketCard.offsetWidth, ticketCard.offsetHeight);
                pdf.save('{{ $ticket->ticket_code }}.pdf');
            });
        }, 200);
    });

    // Download PNG
    document.getElementById('downloadPNG').addEventListener('click', function () {
        const ticketCard = document.querySelector('.ticket-card');

        hideElementsBeforeCapture(); // 🔥 Hide buttons BEFORE capture

        setTimeout(() => {
            html2canvas(ticketCard, { scale: 2 }).then(canvas => {
                const link = document.createElement('a');
                link.download = '{{ $ticket->ticket_code }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        }, 200);
    });
</script>


</body>
</html>
