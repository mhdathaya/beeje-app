<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promo Spesial - Beeje Coffee</title>
    <link rel="manifest" href="{{ asset('images/site.webmanifest') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/components/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
      
        
        .discount-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
            z-index: 10;
        }
        
        .original-price {
            text-decoration: line-through;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .promo-price {
            color: #dc3545;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .promo-card {
            position: relative;
            overflow: hidden;
        }
        
        .promo-timer {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 5px;
        }
        
        /* Carousel Banner */
        .promo-carousel {
            margin-bottom: 10rem;
        }
        
        .carousel-item img {
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }
        
        /* Promo Section Styling */
        .promo-section {
            margin-bottom: 1rem;
        }
        
        .promo-section-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #f8d7da;
            color: #721c24;
        }
        
        .promo-section-subtitle {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 1.5rem;
        }
        
        /* Scrollable Row */
        .scrollable-row {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            padding-bottom: 15px;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 1rem;
        }
        
        .scrollable-row .product-item {
            flex: 0 0 auto;
            width: 280px;
            margin-right: 15px;
        }
        
        .scrollable-row::-webkit-scrollbar {
            height: 6px;
        }
        
        .scrollable-row::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .scrollable-row::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        
        /* View All Button */
        .view-all-btn {
            display: inline-block;
            padding: 5px 15px;
            background-color: #f8d7da;
            color: #721c24;
            border-radius: 20px;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .view-all-btn:hover {
            background-color: #721c24;
            color: white;
        }
        
        /* Promo Badge */
        .promo-name-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #17a2b8;
            color: white;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            z-index: 10;
        }
    </style>
</head>
<body>
    @include('components.header')

    <!-- Banner Carousel -->
   

    <div class="container">
        <!-- Promo Berdasarkan Tipe -->
        @if(isset($typePromos) && count($typePromos) > 0)
        @foreach($typePromos as $typePromo)
        <section class="promo-section">
            <h2 class="promo-section-title">{{ $typePromo['display_name'] }}</h2>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="promo-section-subtitle mb-0">Penawaran spesial untuk kamu</p>
                <a href="#" class="view-all-btn">Lihat Semua</a>
            </div>
            
            <div class="scrollable-row">
                @foreach($typePromo['products'] as $product)
                <div class="product-item">
                    <div class="menu-card promo-card">
                        <span class="discount-badge">-{{ $product->discount_percentage }}%</span>
                        @if($product->promo_name)
                        <span class="promo-name-badge">{{ $product->promo_name }}</span>
                        @endif
                        <div class="menu-image d-flex justify-content-center align-items-center">
                            @if($product->image1)
                                <img src="{{ asset('storage/' . $product->image1) }}" 
                                     class="img-fluid rounded" 
                                     alt="{{ $product->name }}" 
                                     style="width: 100px; height: 100px; object-fit: cover;">
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
                            <p class="original-price">{{ $product->formatted_price }}</p>
                            <p class="promo-price">{{ $product->formatted_promo_price }}</p>
                            @if($product->promo_end)
                            <p class="promo-timer">Berakhir pada: {{ \Carbon\Carbon::parse($product->promo_end)->format('d M Y') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endforeach
        @endif

        <!-- Promo Berdasarkan Kategori -->
        @if(isset($categoryPromos) && count($categoryPromos) > 0)
        @foreach($categoryPromos as $categoryPromo)
        <section class="promo-section">
            <h2 class="promo-section-title">Promo {{ ucfirst($categoryPromo['category']) }}</h2>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="promo-section-subtitle mb-0">Produk {{ $categoryPromo['category'] }} dengan harga spesial</p>
                <a href="#" class="view-all-btn">Lihat Semua</a>
            </div>
            
            <div class="scrollable-row">
                @foreach($categoryPromo['products'] as $product)
                <div class="product-item">
                    <div class="menu-card promo-card">
                        <span class="discount-badge">-{{ $product->discount_percentage }}%</span>
                        @if($product->promo_name)
                        <span class="promo-name-badge">{{ $product->promo_name }}</span>
                        @endif
                        <div class="menu-image d-flex justify-content-center align-items-center">
                            @if($product->image1)
                                <img src="{{ asset('storage/' . $product->image1) }}" 
                                     class="img-fluid rounded" 
                                     alt="{{ $product->name }}" 
                                     style="width: 100px; height: 100px; object-fit: cover;">
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
                            <p class="original-price">{{ $product->formatted_price }}</p>
                            <p class="promo-price">{{ $product->formatted_promo_price }}</p>
                            @if($product->promo_end)
                            <p class="promo-timer">Berakhir pada: {{ \Carbon\Carbon::parse($product->promo_end)->format('d M Y') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endforeach
        @endif

        <!-- Jika tidak ada promo -->
        @if((!isset($categoryPromos) || count($categoryPromos) == 0) && (!isset($typePromos) || count($typePromos) == 0))
        <div class="text-center my-5">
            <h3>Tidak ada produk promo saat ini</h3>
            <p>Silakan cek kembali nanti untuk penawaran menarik!</p>
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>