<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PromoController extends Controller
{
    /**
     * Mendapatkan daftar produk yang sedang promo
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Product::where('is_promo', true)
                        ->where('status', 'active');

        // Filter berdasarkan kategori jika ada
        if ($request->has('category')) {
            $query->where('category', $request->category);
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
        $promoProducts = $query->paginate(10);

        $promoProducts->getCollection()->transform(function ($product) {
            $product->images = [
                'image1' => $product->image1 ? 'storage/' . $product->image1 : null,
                'image2' => $product->image2 ? 'storage/' . $product->image2 : null,
                'image3' => $product->image3 ? 'storage/' . $product->image3 : null
            ];
            
            // Tambahkan path untuk banner promo
            $product->promo_banner_url = $product->promo_banner ? 'storage/' . $product->promo_banner : null;
            
            // Tambahkan format harga Rupiah
            $product->formatted_price = 'Rp ' . number_format($product->price, 0, ',', '.');
            $product->formatted_promo_price = $product->promo_price ? 'Rp ' . number_format($product->promo_price, 0, ',', '.') : null;
            
            // Remove the individual image fields if you don't want them in the response
            unset($product->image1);
            unset($product->image2);
            unset($product->image3);
            unset($product->promo_banner);
            
            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => $promoProducts
        ]);
    }

    /**
     * Mendapatkan detail produk promo berdasarkan ID
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::where('id', $id)
                        ->where('is_promo', true)
                        ->where('status', 'active')
                        ->firstOrFail();

        // Transform image paths for single product
        $product->images = [
            'image1' => $product->image1 ? 'storage/' . $product->image1 : null,
            'image2' => $product->image2 ? 'storage/' . $product->image2 : null,
            'image3' => $product->image3 ? 'storage/' . $product->image3 : null
        ];

        // Tambahkan path untuk banner promo
        $product->promo_banner_url = $product->promo_banner ? 'storage/' . $product->promo_banner : null;

        // Tambahkan format harga Rupiah
        $product->formatted_price = 'Rp ' . number_format($product->price, 0, ',', '.');
        $product->formatted_promo_price = $product->promo_price ? 'Rp ' . number_format($product->promo_price, 0, ',', '.') : null;

        // Remove the individual image fields
        unset($product->image1);
        unset($product->image2);
        unset($product->image3);
        unset($product->promo_banner);

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }
    
    /**
     * Mendapatkan semua banner promo untuk carousel di halaman utama
     *
     * @return \Illuminate\Http\Response
     */
    public function getBanners()
    {
        $promoProducts = Product::where('is_promo', true)
                        ->where('status', 'active')
                        ->whereNotNull('promo_banner')
                        ->select('id', 'name', 'promo_banner', 'promo_description')
                        ->get();

        $banners = $promoProducts->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'banner_url' => 'storage/' . $product->promo_banner,
                'description' => $product->promo_description
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $banners
        ]);
    }

    /**
     * Mendapatkan daftar promo berdasarkan kategori
     *
     * @return \Illuminate\Http\Response
     */
    public function getPromosByCategory()
    {
        // Ambil semua kategori yang memiliki produk promo aktif
        $categories = Product::where('is_promo', true)
                ->where('status', 'active')
                ->select('category')
                ->distinct()
                ->get()
                ->pluck('category');
    
        $result = [];
        
        foreach ($categories as $category) {
            // Ambil produk promo untuk kategori ini
            $products = Product::where('is_promo', true)
                    ->where('status', 'active')
                    ->where('category', $category)
                    ->orderBy('promo_order')
                    ->take(10)
                    ->get();
            
            // Transform produk
            $products->transform(function ($product) {
                $product->images = [
                    'image1' => $product->image1 ? 'storage/' . $product->image1 : null,
                    'image2' => $product->image2 ? 'storage/' . $product->image2 : null,
                    'image3' => $product->image3 ? 'storage/' . $product->image3 : null
                ];
                
                $product->promo_banner_url = $product->promo_banner ? 'storage/' . $product->promo_banner : null;
                $product->formatted_price = 'Rp ' . number_format($product->price, 0, ',', '.');
                $product->formatted_promo_price = $product->promo_price ? 'Rp ' . number_format($product->promo_price, 0, ',', '.') : null;
                
                unset($product->image1);
                unset($product->image2);
                unset($product->image3);
                unset($product->promo_banner);
                
                return $product;
            });
            
            // Tambahkan ke hasil
            if ($products->count() > 0) {
                $result[] = [
                    'category' => $category,
                    'products' => $products
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * Mendapatkan daftar promo berdasarkan tipe promo
     *
     * @return \Illuminate\Http\Response
     */
    public function getPromosByType()
    {
        // Ambil semua tipe promo yang aktif
        $promoTypes = Product::where('is_promo', true)
                ->where('status', 'active')
                ->whereNotNull('promo_type')
                ->select('promo_type', 'promo_name')
                ->distinct()
                ->get()
                ->groupBy('promo_type');
    
        $result = [];
        
        foreach ($promoTypes as $type => $promos) {
            // Gunakan nama promo pertama sebagai nama tampilan
            $displayName = $promos->first()->promo_name ?: 'Promo ' . ucfirst($type);
            
            // Ambil produk untuk tipe promo ini
            $products = Product::where('is_promo', true)
                    ->where('status', 'active')
                    ->where('promo_type', $type)
                    ->orderBy('promo_order')
                    ->take(10)
                    ->get();
            
            // Transform produk
            $products->transform(function ($product) {
                $product->images = [
                    'image1' => $product->image1 ? 'storage/' . $product->image1 : null,
                    'image2' => $product->image2 ? 'storage/' . $product->image2 : null,
                    'image3' => $product->image3 ? 'storage/' . $product->image3 : null
                ];
                
                $product->promo_banner_url = $product->promo_banner ? 'storage/' . $product->promo_banner : null;
                $product->formatted_price = 'Rp ' . number_format($product->price, 0, ',', '.');
                $product->formatted_promo_price = $product->promo_price ? 'Rp ' . number_format($product->promo_price, 0, ',', '.') : null;
                
                unset($product->image1);
                unset($product->image2);
                unset($product->image3);
                unset($product->promo_banner);
                
                return $product;
            });
            
            // Tambahkan ke hasil
            if ($products->count() > 0) {
                $result[] = [
                    'promo_type' => $type,
                    'display_name' => $displayName,
                    'products' => $products
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}