@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Semua Produk</h1>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">+ ADD NEW PRODUCT</a>
        </div>

        <div class="row">
            @foreach($products as $product)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex">
                                @if($product->image1)
                                    <img src="{{ asset('storage/' . $product->image1) }}" class="img-fluid rounded me-3"
                                        alt="{{ $product->name }}" style="width: 100px; height: 100px; object-fit: cover;">
                                @else
                                    <img src="{{ asset('default-image.jpg') }}" class="img-fluid rounded me-3" alt="Default Image"
                                        style="width: 100px; height: 100px; object-fit: cover;">
                                @endif
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h5 class="card-title mb-1">{{ $product->name }}</h5>
                                            <p class="card-text text-muted mb-0">{{ $product->category }}</p>
                                        </div>
                                        <div>
                                            @if($product->is_promo && $product->promo_price)
                                                <h5 class="mb-0 text-danger">Rp
                                                    {{ number_format($product->promo_price, 0, ',', '.') }}</h5>
                                                <p class="text-muted mb-0"><s>Rp
                                                        {{ number_format($product->price, 0, ',', '.') }}</s></p>
                                            @else
                                                <h5 class="mb-0">Rp {{ number_format($product->price, 0, ',', '.') }}</h5>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="dropdown-item">
                                            <i class="fas fa-edit me-2"></i>Edit
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-trash me-2"></i>Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="summary mb-3 mt-3">
                                <h6 class="mb-2">Summary</h6>
                                <p class="text-muted small">
                                    {{ $product->description ?? 'Lorem ipsum is placeholder text commonly used in the graphic.' }}
                                </p>
                                @if($product->is_promo_active && $product->promo_description)
                                    <div class="alert alert-warning py-2 small">
                                        <strong>Promo:</strong> {{ $product->promo_description }}
                                        @if($product->promo_end)
                                            <br><strong>Berakhir:</strong>
                                            {{ \Carbon\Carbon::parse($product->promo_end)->format('d M Y') }}
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="sales-info">
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Sales</span>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-arrow-up text-success me-1"></i>
                                            <span>{{ $product->total_sales ?? 0 }}</span>
                                        </div>
                                    </div>
                                    <div class="progress" style="height: 4px;">
                                        @php
                                            $maxSales = $products->max('total_sales') ?: 1;
                                            $percentage = ($product->total_sales ?? 0) / $maxSales * 100;
                                        @endphp
                                        <div class="progress-bar bg-warning" role="progressbar"
                                            style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>  

                                <div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Remaining Products</span>
                                        <span>{{ $product->stock }}</span>
                                    </div>
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 75%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item {{ $products->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $products->previousPageUrl() }}" aria-label="Previous">
                        <span aria-hidden="true">Previous</span>
                    </a>
                </li>
                <li class="page-item {{ !$products->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $products->nextPageUrl() }}" aria-label="Next">
                        <span aria-hidden="true">Next</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <link rel="stylesheet" href="{{ asset('css/products.css') }}">
@endsection