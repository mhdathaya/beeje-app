<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    private function getCategories()
    {
        return Product::select('category', DB::raw('count(*) as product_count'))
                     ->groupBy('category')
                     ->get();
    }

    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $products = $query->withCount(['orders as total_sales' => function($query) {
            $query->select(DB::raw('COALESCE(SUM(quantity), 0)'));
        }])->paginate(12); // Menggunakan paginate() dengan 12 item per halaman

        $categories = $this->getCategories();

        return view('admin.products', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image1' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_promo' => 'nullable|boolean',
            'promo_price' => 'nullable|numeric|min:0',
            'promo_start' => 'nullable|date',
            'promo_end' => 'nullable|date|after_or_equal:promo_start',
            'promo_description' => 'nullable|string',
            'promo_banner' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->except(['image1', 'image2', 'image3']);
        $data['is_promo'] = $request->has('is_promo');

        // Handle image uploads
        if ($request->hasFile('image1')) {
            $data['image1'] = $request->file('image1')->store('products', 'public');
        }
        if ($request->hasFile('image2')) {
            $data['image2'] = $request->file('image2')->store('products', 'public');
        }
        if ($request->hasFile('image3')) {
            $data['image3'] = $request->file('image3')->store('products', 'public');
        }

        // Handle promo banner upload
        if ($request->hasFile('promo_banner')) {
            $data['promo_banner'] = $request->file('promo_banner')->store('products/banners', 'public');
        }

        Product::create($data);

        return redirect()->route('admin.products')->with('success', 'Product created successfully');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_promo' => 'nullable|boolean',
            'promo_price' => 'nullable|numeric|min:0',
            'promo_start' => 'nullable|date',
            'promo_end' => 'nullable|date|after_or_equal:promo_start',
            'promo_description' => 'nullable|string',
            'promo_banner' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->except(['image1', 'image2', 'image3']);
        $data['is_promo'] = $request->has('is_promo');

        // Handle image updates
        foreach(['image1', 'image2', 'image3'] as $imageField) {
            if ($request->hasFile($imageField)) {
                // Delete old image if exists
                if ($product->$imageField) {
                    Storage::disk('public')->delete($product->$imageField);
                }
                $data[$imageField] = $request->file($imageField)->store('products', 'public');
            }
        }

        // Handle promo banner update
        if ($request->hasFile('promo_banner')) {
        // Delete old banner if exists
        if ($product->promo_banner) {
            Storage::disk('public')->delete($product->promo_banner);
        }
        $data['promo_banner'] = $request->file('promo_banner')->store('products/banners', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products')->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        return redirect()->route('admin.products')->with('success', 'Produk berhasil dihapus');
    }

    public function create()
    {
        $categories = $this->getCategories();
        return view('admin.products.create', compact('categories'));
    }

    public function edit(Product $product)
    {
        $categories = $this->getCategories();
        return view('admin.products.edit', compact('product', 'categories'));
    }

 

    public function show(Product $product)
    {
        $product->loadCount([
            'orders as total_sales' => function($query) {
                $query->select(DB::raw('COALESCE(SUM(order_product.quantity * order_product.price), 0)'));
            },
            'orders as units_sold' => function($query) {
                $query->select(DB::raw('COALESCE(SUM(order_product.quantity), 0)'));
            }
        ]);

        return view('admin.products.show', compact('product'));
    }
}
