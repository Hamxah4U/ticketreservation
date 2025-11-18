<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Event Ticket</title>
    <style>
        body {
            margin: 0; 
            padding: 0; 
            background: #f4f4f4; 
            font-family: Arial, sans-serif; 
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header {
            background: #0d6efd;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .content {
            padding: 25px;
        }
        .qr-box {
            text-align: center;
            margin: 20px 0;
        }
        .qr-box img {
            display: block;
            margin: auto;
            border: 8px solid #eee;
            border-radius: 12px;
            width: 220px;
        }
        .ticket-info {
            width: 100%;
            margin-bottom: 20px;
            font-size: 15px;
        }
        .ticket-info td {
            padding: 5px 0;
        }
        .footer {
            background: #f8f8f8;
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Event Ticket Confirmation</h1>
        </div>
        <div class="content">
            <p>Hello <strong>{{ $ticket->buyer_name }}</strong>,</p>
            <p>Thank you for your purchase! Your event ticket is ready. Please present the QR code below at the event gate.</p>
            <!-- QR CODE -->
            {{-- <div class="qr-box">
                <img src="{{ $qrDataUri }}" alt="QR Code" />
            </div> --}}

            <table class="ticket-info">
                <tr>
                    <td><strong>Ticket Code:</strong></td>
                    <td style="text-align:right;">{{ $ticket->ticket_code }}</td>
                </tr>
                <tr>
                    <td><strong>Email:</strong></td>
                    <td style="text-align:right;">{{ $ticket->buyer_email }}</td>
                </tr>
                <tr>
                    <td><strong>Amount Paid:</strong></td>
                    <td style="text-align:right;">₦{{ number_format($ticket->price, 2) }}</td>
                </tr>
                <p>
                    This ticket is for personal use only. Do NOT allow anyone to scan this QR code. If scanned by another person, the ticket will become invalid
                </p>
            </table>

            <p style="font-size:14px; color:#444;">
                Please save this email. You will need the QR code for verification at the event.
            </p>
        </div>
        <div class="footer">
            Sent by Tikvaah Tech Solutions<br>
            &copy; {{ date('Y') }} All Rights Reserved
        </div>
    </div>
</body>
</html>