<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Beeje Coffee</title>
    <link rel="manifest" href="{{ asset('images/site.webmanifest') }}">

    

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/header.css') }}">

</head>
<body>
    @include('components.header')

    <section class="menu-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Calon menu favorit kamu</h2>
                <p class="section-description">Minuman lezat kami, termasuk pilihan espresso klasik, minuman spesial, dan makanan ringan.</p>
            </div>
            
            <div class="menu-tabs">
                <button class="tab-button active" data-category="all">Semua</button>
                @foreach($categories as $category)
                    <button class="tab-button" data-category="{{ $category->category }}">{{ ucfirst($category->category) }}</button>
                @endforeach
            </div>

            <div class="row g-4" id="product-container">
                @foreach($products as $product)
                <div class="col-md-4 product-item" data-category="{{ $product->category }}">
                    <!-- Di dalam loop foreach products -->
                    <div class="menu-card {{ $product->is_promo_active ? 'promo-card' : '' }}">
                       
                        <div class="menu-image d-flex justify-content-center align-items-center">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" 
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
                            @if($product->is_promo_active)
                            <p class="original-price">{{ $product->formatted_price }}</p>
                            @else
                            <p class="price">{{ $product->formatted_price }}</p>
                            @endif
                            @if($product->is_popular)
                            <span class="popular-badge">Populer!</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="pagination">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.tab-button');
        const products = document.querySelectorAll('.product-item');
    
        buttons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons
                buttons.forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                button.classList.add('active');
    
                const category = button.getAttribute('data-category');
    
                products.forEach(product => {
                    if (category === 'all') {
                        product.style.display = '';
                    } else {
                        if (product.getAttribute('data-category') === category) {
                            product.style.display = '';
                        } else {
                            product.style.display = 'none';
                        }
                    }
                });
            });
        });
    });
    </script>
</body>
</html>
