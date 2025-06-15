
<header class="header">
    <div class="header-container">
        <a href="{{ route('beranda') }}" class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="Beeje Coffee" class="logo-img">
            <span class="logo-text">Beeje Coffee</span>
        </a>
        
        <nav class="nav-menu">
            <a href="{{ route('beranda') }}" class="nav-item {{ request()->routeIs('beranda') ? 'active' : '' }}">
                Beranda
            </a>
            <a href="{{ route('menu') }}" class="nav-item {{ request()->routeIs('menu') ? 'active' : '' }}">
                Menu
            </a>
            <a href="{{ route('promo') }}" class="nav-item {{ request()->routeIs('promo') ? 'active' : '' }}">
                Promo
            </a>
           
            <a href="{{ route('admin.login') }}" class="nav-item {{ request()->routeIs('admin.login') ? 'active' : '' }}">
                Login
            </a>
        </nav>
        
        <div class="header-buttons">
            @auth
                <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn-login">Logout</button>
                </form>
            @endauth
            <a href="{{ route('hubungi-kami') }}" class="btn-hubungi">Hubungi Kami</a>
        </div>
    </div>
</header>