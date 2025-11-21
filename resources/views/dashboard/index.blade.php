<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #343a40;
            color: #fff;
            padding-top: 20px;
        }
        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: #fff;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: #495057;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
    </style>
</head>
<body>
ddddddddddd
<div class="sidebar">
    <h4 class="text-center mb-4">Admin Panel</h4>
    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
    {{-- <a href="{{ route('admin.tickets') }}">Tickets</a> --}}
    <a href="{{ route('dashboard.scan') }}">QR Validator</a>
    {{-- <a href="{{ route('admin.users') }}">Users</a> --}}

    <form method="POST" action="{{ route('admin.logout') }}" class="mt-4 text-center">
        @csrf
        <button class="btn btn-danger btn-sm">Logout</button>
    </form>
</div>

<div class="content">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
