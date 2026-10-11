<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Portal') - Pooja Nilayam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .admin-sidebar {
            width: 260px;
            min-height: 100vh;
        }
        @media (max-width: 991.98px) {
            .admin-sidebar {
                min-height: auto;
                width: 100%;
            }
        }
    </style>
</head>
<body class="bg-light">
<div class="d-flex flex-column flex-lg-row min-vh-100">
    <!-- Sidebar -->
    <aside class="admin-sidebar bg-dark text-white p-3 d-flex flex-column flex-shrink-0 shadow-sm">
        <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center mb-3 mb-md-4 text-white text-decoration-none">
            <i class="bi bi-shield-lock-fill fs-3 text-warning me-2"></i>
            <span class="fs-5 fw-bold tracking-wide">Pooja Nilayam Admin</span>
        </a>
        <hr class="border-secondary mt-0 mb-3">
        
        <ul class="nav nav-pills flex-column mb-auto gap-1">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('admin.bookings.index') }}" class="nav-link text-white {{ request()->routeIs('admin.bookings.*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-calendar-check me-2"></i> Pooja Bookings
                </a>
            </li>
            <li>
                <a href="{{ route('admin.poojas.index') }}" class="nav-link text-white {{ request()->routeIs('admin.poojas.*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-flower1 me-2"></i> Pooja Catalog
                </a>
            </li>
            <li>
                <a href="{{ route('admin.orders.index') }}" class="nav-link text-white {{ request()->routeIs('admin.orders.*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-box-seam me-2"></i> Store Orders
                </a>
            </li>
            <li>
                <a href="{{ route('admin.products.index') }}" class="nav-link text-white {{ request()->routeIs('admin.products.*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-tags me-2"></i> Product Catalog
                </a>
            </li>
            <li>
                <a href="{{ route('admin.verifications.pujaris.index') }}" class="nav-link text-white {{ request()->routeIs('admin.verifications.pujaris.*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-person-badge me-2"></i> Pujari Verifications
                </a>
            </li>
            <li>
                <a href="{{ route('admin.temples.index') }}" class="nav-link text-white {{ request()->routeIs('admin.temples.*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-bank me-2"></i> Temples
                </a>
            </li>
            <li>
                <a href="{{ route('admin.astrology.index') }}" class="nav-link text-white {{ request()->routeIs('admin.astrology.*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-stars me-2"></i> Astrology Sessions
                </a>
            </li>
            <li>
                <a href="{{ route('admin.digital-poojas.index') }}" class="nav-link text-white {{ request()->routeIs('admin.digital-poojas.*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-camera-video me-2"></i> Digital & Live Poojas
                </a>
            </li>
            <li>
                <a href="{{ route('admin.settlements.index') }}" class="nav-link text-white {{ request()->routeIs('admin.settlements.*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-wallet2 me-2"></i> Finance & Payouts
                </a>
            </li>
            <li>
                <a href="{{ route('admin.support.index') }}" class="nav-link text-white {{ request()->routeIs('admin.support.*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-headset me-2"></i> Support Tickets
                </a>
            </li>
            <li>
                <a href="{{ route('admin.cms.banners') }}" class="nav-link text-white {{ request()->routeIs('admin.cms.*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-window-sidebar me-2"></i> CMS Banners
                </a>
            </li>
        </ul>

        <hr class="border-secondary mt-3 mb-3">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="bi bi-person-circle fs-4 me-2 text-secondary"></i>
                <span class="small fw-semibold text-truncate" style="max-width: 120px;">{{ auth()->user()->name ?? 'Administrator' }}</span>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow-1 p-3 p-md-4 overflow-auto">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Please check the errors below:</strong>
                <ul class="mb-0 mt-1 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>