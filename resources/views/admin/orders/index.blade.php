@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">Orders List</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Order List</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="date-range px-3 py-2 bg-light rounded-pill">
                <span>Feb 16,2022 - Feb 20,2022</span>
            </div>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    Change Status
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">All</a></li>
                    <li><a class="dropdown-item" href="#">Delivered</a></li>
                    <li><a class="dropdown-item" href="#">Canceled</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="mb-0">Recent Purchases</h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-link"><i class="fas fa-ellipsis-v"></i></button>
                    <a href="{{ route('admin.orders.pdf') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-file-pdf me-1"></i> Download PDF
                    </a>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox">
                                </div>
                            </th>
                            <th>Product</th>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Customer Name</th>
                            <th>Status</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox">
                                </div>
                            </td>
                            <td>{{ $order->products->pluck('name')->first() }}</td>
                            <td>#{{ $order->order_number }}</td>
                            <td>{{ $order->created_at->format('M jS, Y') }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($order->user->name) }}" 
                                         class="rounded-circle me-2" width="30">
                                    {{ $order->user->name }}
                                </div>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <span class="badge rounded-pill dropdown-toggle status-badge-{{ $order->id }} 
                                        {{ $order->status == 'delivered' ? 'bg-success' : 
                                       ($order->status == 'canceled' ? 'bg-danger' : 
                                       ($order->status == 'paid' ? 'bg-info' : 'bg-warning')) }}" 
                                      data-bs-toggle="dropdown" 
                                      style="cursor: pointer;">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0)" class="dropdown-item status-update" 
                                               data-order-id="{{ $order->id }}" 
                                               data-status="pending">
                                                <span class="badge bg-warning me-2">•</span> Pending
                                            </a>
                                            <a href="javascript:void(0)" class="dropdown-item status-update" 
                                               data-order-id="{{ $order->id }}" 
                                               data-status="paid">
                                                <span class="badge bg-info me-2">•</span> Paid
                                            </a>
                                            <a href="javascript:void(0)" class="dropdown-item status-update" 
                                               data-order-id="{{ $order->id }}" 
                                               data-status="delivered">
                                                <span class="badge bg-success me-2">•</span> Delivered
                                            </a>
                                            <a href="javascript:void(0)" class="dropdown-item status-update" 
                                               data-order-id="{{ $order->id }}" 
                                               data-status="canceled">
                                                <span class="badge bg-danger me-2">•</span> Canceled
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Hapus atau modifikasi bagian pagination berikut -->
            <div class="d-flex justify-content-center mt-4">
                <!-- Pagination dihapus karena menampilkan semua data -->
                <p class="text-muted">Menampilkan semua {{ count($orders) }} data order</p>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            <p class="text-muted mb-0">Menampilkan {{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() }} data</p>
        </div>
        <div class="d-flex gap-2">
            @if($orders->onFirstPage())
                <button class="btn btn-light px-4 py-2" disabled>
                    <i class="fas fa-chevron-left me-1"></i> Previous
                </button>
            @else
                <a href="{{ $orders->previousPageUrl() }}" class="btn btn-light px-4 py-2">
                    <i class="fas fa-chevron-left me-1"></i> Previous
                </a>
            @endif
            
            @if($orders->hasMorePages())
                <a href="{{ $orders->nextPageUrl() }}" class="btn btn-light px-4 py-2">
                    Next <i class="fas fa-chevron-right ms-1"></i>
                </a>
            @else
                <button class="btn btn-light px-4 py-2" disabled>
                    Next <i class="fas fa-chevron-right ms-1"></i>
                </button>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.badge {
    padding: 8px 12px;
}
.table th {
    font-weight: 500;
    color: #666;
}
.page-link {
    color: #333;
    padding: 8px 12px;
}
.page-item.active .page-link {
    background-color: #333;
    border-color: #333;
}
.btn-light {
    background-color: #f8f9fa;
    border-color: #f8f9fa;
}
.breadcrumb {
    margin-bottom: 0;
}
.date-range {
    font-size: 14px;
}
.dropdown-item {
    display: flex;
    align-items: center;
    padding: 8px 16px;
}
.dropdown-item:hover {
    background-color: #f8f9fa;
}
.badge.dropdown-toggle::after {
    margin-left: 8px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusForms = document.querySelectorAll('.status-form');
    
    statusForms.forEach(form => {
        form.querySelectorAll('button').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                if(confirm('Are you sure you want to change the order status?')) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    document.querySelectorAll('.status-update').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const orderId = this.dataset.orderId;
            const status = this.dataset.status;
            
            if(confirm('Are you sure you want to change the order status?')) {
                fetch(`/admin/orders/${orderId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ status: status })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        const badge = document.querySelector(`.status-badge-${orderId}`);
                        // Update badge color
                        badge.classList.remove('bg-warning', 'bg-success', 'bg-danger', 'bg-info');
                        badge.classList.add(
                            status === 'delivered' ? 'bg-success' : 
                            status === 'canceled' ? 'bg-danger' : 
                            status === 'paid' ? 'bg-info' : 'bg-warning'
                        );
                        // Update badge text
                        badge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                    } else {
                        alert('Failed to update status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to update status');
                });
            }
        });
    });
});
</script>
@endpush