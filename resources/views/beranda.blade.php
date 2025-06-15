<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Beeje Coffee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/components/header.css') }}">
    <link rel="manifest" href="{{ asset('images/site.webmanifest') }}">

    <link rel="stylesheet" href="{{ asset('css/beranda.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    @include('components.header')

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="content-wrapper">
            <h1 class="hero-title">
                <span class="pure-love">PURE LOVE</span><br>
                <span class="of">OF</span> <span class="coffee">COFFEE</span>
            </h1>
            <p class="tagline">"Satu Gelas Penuh Makna"</p>
            <p class="description">
                Di Beeje Coffee, kami percaya bahwa setiap cangkir kopi
                memiliki cerita. Cerita tentang pagi yang penuh semangat,
                waktu santai yang hangat, atau momen berbagi bersama
                orang tercinta. Kami hadir untuk menciptakan pengalaman
                yang berharga untuk anda.
            </p>
            <a href="{{ url('admin/login') }}" class="btn btn-order">Order Now <span class="arrow">→</span></a>
            <div class="coffee-beans-bottom">
                <img src="{{ asset('images/kopi.png') }}" alt="Coffee Beans" class="beans-bottom-decoration">
            </div>
        </div>

        <div class="right-hero">
            <div class="right-hero-wrapper">
                <div class="semi-circle-bg"></div>
                <div class="coffee-image">
                    <img src="{{ asset('images/beeje.png') }}" alt="Beeje Coffee">
                </div>
                <div class="beeje-text-stack">
                   
                    <span class="outline">BEEJE COFFEE</span>
                    <span class="filled">BEEJE COFFEE</span>
                </div>
            </div>
        </div>
    </section>
    <!-- Menu Section -->
    <section class="menu-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Jelajahi menu andalan kami</h2>
                <p class="section-description">Minuman lezat kami, termasuk pilihan espresso klasik, minuman spesial, dan makanan ringan.</p>
            </div>
            
            <div class="row g-4" id="product-container">
                @foreach($products as $product)
                <div class="col-md-4 product-item" data-category="{{ $product->category }}">
                    <div class="menu-card">
                        <div class="menu-image d-flex justify-content-center align-items-center">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" 
                                     class="img-fluid rounded" 
                                     alt="{{ $product->name }}" 
                                     style="width: 200px; height: 700px; object-fit: cover;">
                            @else
                                <img src="{{ asset('default-image.jpg') }}" 
                                     class="img-fluid rounded" 
                                     alt="Default Image" 
                                     style="width: 100px; height: 100px; object-fit: cover;">
                            @endif
                            <span class="category-badge {{ strtolower($product->category) }}">{{ $product->category }}</span>
                        </div>
                        <div class="menu-info">
                            <h3>{{ $product->name }}</h3>
                            <p class="price">{{ 'Rp ' . number_format($product->price, 0, ',', '.') }}</p>
                            @if($product->is_popular)
                            <span class="popular-badge">Populer!</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="explore-banner">
            <div class="text-section">
                <h3>Ayo cek menu terbaik kami!</h3>
                <a href="{{ url('/menu') }}" class="btn-explore">
                    Jelajahi
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="image-section">
                <img src="{{ asset('images/menu.png') }}" alt="Menu Beeje">
            </div>
        </div>
        
        
    </section>
   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
