<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order; // Assuming you will need the Order model
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch orders with relationships and paginate
        $orders = Order::with(['user', 'products', 'deliveryOrder', 'reservation'])
            ->orderBy('created_at', 'desc')
            ->paginate(10); // Menggunakan paginate untuk navigasi halaman
    
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Logic to show form for creating an order (less common for admin panel)
        // return view('admin.orders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Logic to store a new order (less common for admin panel)
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        // Logic to display a specific order for the admin panel
        // Load relationships if needed
        $order->load(['user', 'products', 'deliveryOrder', 'reservation']);

        return view('admin.orders.show', compact('order'));

        // If you intended this to be an API endpoint for admin, return JSON:
        // return response()->json([
        //     'success' => true,
        //     'data' => $order
        // ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        // Logic to show form for editing an order
        return view('admin.orders.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        // Logic to update an order
        // Example: Update status
        // $request->validate(['status' => 'required|in:pending,processing,completed,canceled']); // Use 'canceled' as per migration
        // $order->status = $request->status;
        // $order->save();

        // return redirect()->route('admin.orders.index')->with('success', 'Order updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        // Logic to delete an order
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully');

        // If you intended this to be an API endpoint for admin, return JSON:
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Order deleted successfully'
        // ]);
    }

    // You might also need an updateStatus method if you have a route for it
    public function generatePdf()
    {
        $orders = Order::with(['user', 'products'])
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('admin.orders.pdf', compact('orders'));
        return $pdf->download('orders-report.pdf');
    }

    public function updateStatus(Request $request, $id)
    {
        // Example logic to update order status
        $request->validate([
            'status' => 'required|in:pending,paid,delivered,canceled' // Match enum in orders table
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        // Redirect back or return a response
        // return back()->with('success', 'Order status updated successfully.');

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => $order
        ]);
    }


}