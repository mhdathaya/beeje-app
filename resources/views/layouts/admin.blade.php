<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejee Coffee - Admin Dashboard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}"> -->
    <link rel="manifest" href="{{ asset('images/site.webmanifest') }}">

    <!-- Custom CSS -->
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h4 class="text-center mb-4">Bejee Coffee</h4>
                    <nav class="nav flex-column">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.products') ? 'active' : '' }}" href="{{ route('admin.products') }}">
                            <i class="fas fa-box me-2"></i> All Products
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.orders') ? 'active' : '' }}" href="{{ route('admin.orders') }}">
                            <i class="fas fa-shopping-cart me-2"></i> Order List
                        </a>
                        <div class="nav-item">
                            <a class="nav-link d-flex justify-content-between align-items-center" 
                               data-bs-toggle="collapse" href="#categoriesCollapse" role="button">
                                <span><i class="fas fa-tags me-2"></i> Categories</span>
                                <i class="fas fa-chevron-down"></i>
                            </a>
                            <div class="collapse show" id="categoriesCollapse">
                                <div class="categories-list">
                                    @foreach($categories as $category)
                                        <a href="{{ route('admin.products', ['category' => $category->category]) }}" class="category-item d-flex justify-content-between align-items-center">
                                            <span>{{ $category->category }}</span>
                                            <span class="category-count">{{ $category->product_count }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-0 main-content">
                <!-- Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light px-4">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <!-- Search Form -->
                        <form class="d-flex me-auto" action="{{ route('admin.search') }}" method="GET">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Cari..." name="q" value="{{ request('q') }}">
                                <button class="btn btn-brown" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>

                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle"></i> Admin
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('admin.logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>

                <!-- Page Content -->
                <div class="p-4">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Before closing body tag -->
    @stack('scripts')
    </body>
    </html>

    <style>
        .btn-brown {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-brown:hover {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .input-group {
            max-width: 300px;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(107, 68, 35, 0.25);
        }
    </style>

    <meta name="csrf-token" content="{{ csrf_token() }}">