@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Hasil Pencarian: "{{ $query }}"</h1>
    </div>

    <!-- Hasil Pencarian Produk -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Produk</h5>
            @if($products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Gambar</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr>
                                <td>
                                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                </td>
                                <td>{{ $product->name }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $product->category }}</span>
                                </td>
                                <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td>{{ $product->stock }}</td>
                                <td>
                                    @if($product->status == 'active')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-info me-2">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">Tidak ada produk yang ditemukan.</p>
            @endif
        </div>
    </div>

    <!-- Hasil Pencarian Pesanan -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3">Pesanan</h5>
            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID Pesanan</th>
                                <th>Pelanggan</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td>#{{ $order->order_id }}</td>
                                <td>{{ $order->customer->name }}</td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                                <td>
                                    <span class="badge {{ $order->status == 'completed' ? 'bg-success' : 'bg-warning' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">Tidak ada pesanan yang ditemukan.</p>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .btn-brown {
        background-color: var(--primary-color);
        color: white;
    }
    .btn-brown:hover {
        background-color: var(--secondary-color);
        color: white;
    }
</style>
@endpush
@endsection