<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get current month and previous month
        $currentMonth = Carbon::now();
        $previousMonth = Carbon::now()->subMonth();
        
        // Default view type is weekly
        $viewType = $request->input('view_type', 'weekly');
        
        // Get sales data based on view type
        switch ($viewType) {
            case 'weekly':
                $salesData = $this->getWeeklySalesData();
                break;
            case 'yearly':
                $salesData = $this->getYearlySalesData();
                break;
            case 'monthly':
            default:
                $salesData = $this->getMonthlySalesData();
                break;
        }
        
        // Get order statistics based on total amounts for current month
        $totalOrders = Order::whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->sum('total_amount');
            
        $activeOrders = Order::where('status', 'pending')
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->sum('total_amount');
            
        // Completed orders include both paid and delivered statuses
        $completedOrders = Order::where(function($query) {
                $query->where('status', 'delivered')
                    ->orWhere('status', 'paid');
            })
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->sum('total_amount');
            
        $returnOrders = Order::where('status', 'canceled')
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->sum('total_amount');
        
        // Get order statistics for previous month
        $previousTotalOrders = Order::whereMonth('created_at', $previousMonth->month)
            ->whereYear('created_at', $previousMonth->year)
            ->sum('total_amount');
            
        $previousActiveOrders = Order::where('status', 'pending')
            ->whereMonth('created_at', $previousMonth->month)
            ->whereYear('created_at', $previousMonth->year)
            ->sum('total_amount');
            
        $previousCompletedOrders = Order::where(function($query) {
                $query->where('status', 'delivered')
                    ->orWhere(function($q) {
                        $q->where('status', 'paid')
                           ->where('payment_status', 'completed');
                    });
            })
            ->whereMonth('created_at', $previousMonth->month)
            ->whereYear('created_at', $previousMonth->year)
            ->sum('total_amount');
            
        $previousReturnOrders = Order::where('status', 'canceled')
            ->whereMonth('created_at', $previousMonth->month)
            ->whereYear('created_at', $previousMonth->year)
            ->sum('total_amount');
        
        // Calculate percentage changes
        $totalOrdersPercentage = $this->calculatePercentageChange($previousTotalOrders, $totalOrders);
        $activeOrdersPercentage = $this->calculatePercentageChange($previousActiveOrders, $activeOrders);
        $completedOrdersPercentage = $this->calculatePercentageChange($previousCompletedOrders, $completedOrders);
        $returnOrdersPercentage = $this->calculatePercentageChange($previousReturnOrders, $returnOrders);
        
        // Get best sellers with total revenue
        $topProducts = Product::withCount(['orders as total_sales' => function($query) {
            $query->select(DB::raw('COALESCE(SUM(order_product.quantity * order_product.price), 0)'));
        }])
        ->orderBy('total_sales', 'desc')
        ->take(3)
        ->get();
    
        // Get recent orders with links
        $recentOrders = Order::with(['user', 'products'])
            ->orderBy('created_at', 'desc')
            ->take(7)
            ->get();
    
        return view('admin.dashboard', compact(
            'salesData',
            'viewType',
            'totalOrders',
            'activeOrders',
            'completedOrders',
            'returnOrders',
            'totalOrdersPercentage',
            'activeOrdersPercentage',
            'completedOrdersPercentage',
            'returnOrdersPercentage',
            'previousMonth',
            'topProducts',
            'recentOrders'
        ));
    }
    
    /**
     * Get weekly sales data for the current month
     */
    private function getWeeklySalesData()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        
        $weeks = [];
        $labels = [];
        $data = [];
        
        // Create weeks for current month
        $currentDate = $startOfMonth->copy();
        $weekNumber = 1;
        
        while ($currentDate->lte($endOfMonth)) {
            $weekStart = $currentDate->copy();
            $weekEnd = $currentDate->copy()->endOfWeek()->min($endOfMonth);
            
            $weekLabel = 'Week ' . $weekNumber;
            $labels[] = $weekLabel;
            
            // Get sales for this week - only completed orders
            $sales = Order::whereBetween('created_at', [$weekStart, $weekEnd])
                ->where(function($query) {
                    $query->where('status', 'delivered')
                        ->orWhere('status', 'paid');
                })
                ->sum('total_amount');
            
            $data[] = $sales / 1000000; // Convert to millions
            
            // Move to next week
            $currentDate = $weekEnd->copy()->addDay();
            $weekNumber++;
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
    
    /**
     * Get monthly sales data for the current year
     */
    private function getMonthlySalesData()
    {
        $year = Carbon::now()->year;
        $months = [];
        $data = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $monthName = Carbon::create($year, $month, 1)->format('M');
            $months[] = $monthName;
            
            $sales = Order::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->where(function($query) {
                    $query->where('status', 'delivered')
                        ->orWhere('status', 'paid');
                })
                ->sum('total_amount');
            
            $data[] = $sales / 1000000; // Convert to millions
        }
        
        return [
            'labels' => $months,
            'data' => $data
        ];
    }
    
    /**
     * Get yearly sales data for the last 5 years
     */
    private function getYearlySalesData()
    {
        $currentYear = Carbon::now()->year;
        $years = [];
        $data = [];
        
        for ($i = 0; $i < 5; $i++) {
            $year = $currentYear - $i;
            $years[] = (string) $year;
            
            $sales = Order::whereYear('created_at', $year)
                ->where(function($query) {
                    $query->where('status', 'delivered')
                        ->orWhere('status', 'paid');
                })
                ->sum('total_amount');
            
            $data[] = $sales / 1000000; // Convert to millions
        }
        
        // Reverse arrays to show oldest year first
        $years = array_reverse($years);
        $data = array_reverse($data);
        
        return [
            'labels' => $years,
            'data' => $data
        ];
    }
    
    /**
     * Calculate percentage change between two values
     *
     * @param float $oldValue
     * @param float $newValue
     * @return float
     */
    private function calculatePercentageChange($oldValue, $newValue)
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }
        
        return round((($newValue - $oldValue) / $oldValue) * 100, 1);
    }

    public function getProductsByCategory($category)
    {
        $products = Product::where('category', $category)
                          ->orderBy('created_at', 'desc')
                          ->get();

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        
        // Mencari produk
        $products = Product::where('name', 'like', "%{$query}%")
                         ->orWhere('description', 'like', "%{$query}%")
                         ->get();
        
        // Mencari pesanan
        $orders = Order::where('id', 'like', "%{$query}%")
                      ->orWhereHas('products', function($q) use ($query) {
                          $q->where('name', 'like', "%{$query}%");
                      })
                      ->get();
        
        return view('admin.search', compact('query', 'products', 'orders'));
    }
}