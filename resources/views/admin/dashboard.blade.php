@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold">Dashboard</h4>
            <small>Home > Dashboard</small>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span>{{ now()->format('F d, Y') }}</span>
            <input type="date" class="form-control form-control-sm" value="{{ now()->toDateString() }}">
            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-bell"></i></button>
            <img src="https://ui-avatars.com/api/?name=Admin" class="rounded-circle" width="35">
        </div>
    </div>

    <!-- Statistic Cards -->
    <div class="row mb-4">
        @php
            $orderData = [
                'Total Orders' => ['value' => $totalOrders, 'percentage' => $totalOrdersPercentage],
                'Active Orders' => ['value' => $activeOrders, 'percentage' => $activeOrdersPercentage],
                'Completed Orders' => ['value' => $completedOrders, 'percentage' => $completedOrdersPercentage],
                'Return Orders' => ['value' => $returnOrders, 'percentage' => $returnOrdersPercentage]
            ];
        @endphp
        
        @foreach($orderData as $title => $data)
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">{{ $title }}</h6>
                        <h4>Rp {{ number_format($data['value'], 0, ',', '.') }}</h4>
                        <small class="text-muted">Dibandingkan {{ $previousMonth->format('M Y') }}</small>
                    </div>
                    <div class="{{ $data['percentage'] >= 0 ? 'text-success' : 'text-danger' }}">
                        <i class="fas fa-arrow-{{ $data['percentage'] >= 0 ? 'up' : 'down' }}"></i> 
                        {{ abs($data['percentage']) }}%
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Modify the sales chart script -->

    <!-- Sales Graph and Best Sellers -->
    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Sale Graph</h5>
                        <div class="btn-group">
                            <a href="{{ route('admin.dashboard', ['view_type' => 'weekly']) }}" class="btn btn-sm px-3 {{ $viewType == 'weekly' ? 'btn-brown active' : 'btn-light' }}">WEEKLY</a>
                            <a href="{{ route('admin.dashboard', ['view_type' => 'monthly']) }}" class="btn btn-sm px-3 {{ $viewType == 'monthly' ? 'btn-brown active' : 'btn-light' }}">MONTHLY</a>
                            <a href="{{ route('admin.dashboard', ['view_type' => 'yearly']) }}" class="btn btn-sm px-3 {{ $viewType == 'yearly' ? 'btn-brown active' : 'btn-light' }}">YEARLY</a>
                        </div>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Best Sellers</h5>
                        <button class="btn btn-brown btn-sm">REPORT</button>
                    </div>
                    @foreach($topProducts as $product)
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('storage/' . $product->image) }}" class="rounded" style="width: 45px; height: 45px; object-fit: cover;">
                        <div class="ms-3 flex-grow-1">
                            <h6 class="mb-0">{{ $product->name }}</h6>
                            <small class="text-muted">{{ $product->total_sales }} sales</small>
                        </div>
                        <div class="text-end">
                            <h6 class="mb-0">Rp {{ number_format($product->price, 0, ',', '.') }}</h6>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Recent Orders</h5>
                <a href="{{ route('admin.orders.report') }}" class="btn btn-brown btn-sm">TRANSACTION REPORT</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead class="text-muted">
                        <tr>
                            <th>Product</th>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                        <tr>
                            <td>
                                @foreach($order->products as $product)
                                    {{ $product->name }}@if(!$loop->last), @endif
                                @endforeach
                            </td>
                            <td>#{{ $order->order_number }}</td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                            <td>
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($order->user->name) }}" width="30" class="rounded-circle me-2">
                                {{ $order->user->name }}
                            </td>
                            <td>
                                <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'canceled' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesData = @json($salesData);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesData.labels,
            datasets: [{
                label: 'Completed Orders Revenue',
                data: salesData.data, // Data sudah dalam format jutaan
                borderColor: '#4361ee',
                backgroundColor: 'rgba(67, 97, 238, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '#4361ee',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    min: 0, // Mulai dari 0 (bukan 1 juta)
                    max: Math.max(...salesData.data, 10) + 1, // Maksimum nilai data + 1 juta (minimal 10 juta)
                    grid: {
                        color: '#f0f0f0'
                    },
                    ticks: {
                        stepSize: 1, // Kenaikan per 1 juta
                        callback: function(value) {
                            return 'Rp ' + value + ' Jt';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.raw.toFixed(2) + ' Jt';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.btn-group .btn {
    border-radius: 20px !important;
    margin: 0 2px;
}
.btn-group .btn.active {
    background-color: #6B4423;
    color: white;
    border-color: #6B4423;
}
.btn-light {
    background-color: #f8f9fa;
    border-color: #f8f9fa;
}
</style>
@endpush
