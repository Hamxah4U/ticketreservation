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
                    <h3 class="mb-0">User Login</h3>
                </div>

                <div class="card-body p-4">

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form id="loginForm_" action="{{ route('admin.login.post') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input 
                                type="email" 
                                name="email" 
                                class="form-control form-control-lg" 
                                value="{{ old('buyer_email') }}" 
                                placeholder="Enter your email"
                                >
                            <x-form-error name="email" />
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" id="" class="form-control">
                            <x-form-error name="password" />
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3">
                            Login
                        </button>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
