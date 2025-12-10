<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Buy Ticket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-primary text-white text-center py-3 rounded-top-4">
                    <h3 class="mb-0">ARABIAN FESTIVAL TICKET RESERVATION</h3>
                </div>

                <div class="card-body p-4">

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('tickets.create') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input 
                                type="text" 
                                name="buyer_name" 
                                class="form-control form-control-lg" 
                                value="{{ old('buyer_name') }}" 
                                placeholder="Enter your full name"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input 
                                type="email" 
                                name="buyer_email" 
                                class="form-control form-control-lg" 
                                value="{{ old('buyer_email') }}" 
                                placeholder="Enter your email"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ticket Type</label>
                            <select id="ticketTypeSelect" name="tickettype_id" class="form-select form-select-lg" required>
                                <option value="" disabled selected>Select a ticket type</option>
                                @foreach($ticketTypes as $type)
                                    <option value="{{ $type->id }}" data-price="{{ $type->price }}">
                                        {{ $type->ticket_type_name }} - ₦{{ number_format($type->price, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Ticket Price (NGN)</label>
                            <input 
                                type="number" 
                                name="price" 
                                class="form-control form-control-lg"
                                step="0.01" 
                                value="" 
                                required
                                readonly>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3">
                            Pay with Paystack
                        </button>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.getElementById('ticketTypeSelect').addEventListener('change', function () {
    let price = this.options[this.selectedIndex].getAttribute('data-price');
    document.querySelector('input[name="price"]').value = price;
});
</script>


</body>
</html>
