<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // Filter berdasarkan kategori
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter berdasarkan status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Pencarian berdasarkan nama atau deskripsi
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Pengurutan
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Ambil data dan ubah semua image menjadi full path
        $products = $query->paginate(10);

        // Di dalam method index dan show, tambahkan:

        $products->getCollection()->transform(function ($product) {
            $product->images = [
                'image1' => $product->image1 ? 'storage/' . $product->image1 : null,
                'image2' => $product->image2 ? 'storage/' . $product->image2 : null,
                'image3' => $product->image3 ? 'storage/' . $product->image3 : null
            ];
            
            // Tambahkan format harga Rupiah
            $product->formatted_price = 'Rp ' . number_format($product->price, 0, ',', '.');
            
            // Remove the individual image fields if you don't want them in the response
            unset($product->image1);
            unset($product->image2);
            unset($product->image3);
            
            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        // Transform image paths for single product
        $product->images = [
            'image1' => $product->image1 ? 'storage/' . $product->image1 : null,
            'image2' => $product->image2 ? 'storage/' . $product->image2 : null,
            'image3' => $product->image3 ? 'storage/' . $product->image3 : null
        ];

        // Remove the individual image fields
        unset($product->image1);
        unset($product->image2);
        unset($product->image3);

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    public function getCategories()
    {
        $categories = Product::select('category')
            ->distinct()
            ->get()
            ->pluck('category');

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}
