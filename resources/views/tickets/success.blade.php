<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Purchased Successfully</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7fa;
        }
        .ticket-card {
            max-width: 450px;
            margin: 40px auto;
            padding: 25px;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 6px 22px rgba(0,0,0,0.1);
            text-align: center;
        }
        .qr-box {
            padding: 12px;
            background: #f1f1f1;
            border-radius: 10px;
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>

<div class="ticket-card">

    <h3 class="mb-1 text-success fw-bold">Payment Successful 🎉</h3>
    <p class="text-muted">Your event ticket has been generated.</p>

    {{-- QR CODE (INLINE) --}}
    <div class="qr-box my-3">
        <img src="{{ $dataUri ?? '' }}" width="200" alt="QR Code">
    </div>

    <h4 class="fw-bold mt-3">{{ $ticket->ticket_code }}</h4>

    <hr>

    <p class="mb-1"><strong>Name:</strong> {{ $ticket->buyer_name }}</p>
    <p class="mb-1"><strong>Email:</strong> {{ $ticket->buyer_email }}</p>
    <p class="mb-1">
        <strong>Amount Paid:</strong> ₦{{ number_format($ticket->price, 2) }}
    </p>

    <hr>

    <p class="text-muted small">A copy of your ticket has also been sent to your email.</p>

    <a href="{{ url('/buy') }}" class="btn btn-primary btn-lg mt-2">Go Home</a>
</div>

</body>
</html>
