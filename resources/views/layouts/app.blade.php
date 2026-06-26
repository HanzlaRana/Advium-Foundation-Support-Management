<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">

            <a class="navbar-brand" href="{{ route('dashboard') }}">
                Admin Panel
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">

                <ul class="navbar-nav me-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('beneficiaries.index') }}">Beneficiaries</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('beneficiaries.create') }}">Add Beneficiary</a>
                    </li>

                </ul>

                <!-- RIGHT SIDE (Logout) -->
                <ul class="navbar-nav">

                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-danger btn-sm">
                                Logout
                            </button>
                        </form>
                    </li>

                </ul>

            </div>
        </div>
    </nav>

    <!-- PAGE CONTENT -->
    <div class="container mt-4">
    @yield('content')
</div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>