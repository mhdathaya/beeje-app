<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category',
        'price',
        'stock',
        'description',
        'image1',
        'image2',
        'image3',
        'status',
        'is_promo',
        'promo_price',
        'promo_start',
        'promo_end',
        'promo_description',
        'promo_banner',
        'promo_name',
        'promo_type',
        'promo_order',
    ];

    // Add this accessor to get the first image
    public function getImageAttribute()
    {
        return $this->image1;
    }
    
    // Accessor untuk format harga dalam Rupiah
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
    
    // Accessor untuk format harga promo dalam Rupiah
    public function getFormattedPromoPriceAttribute()
    {
        if (!$this->promo_price) return null;
        return 'Rp ' . number_format($this->promo_price, 0, ',', '.');
    }
    
    // Accessor untuk mengecek apakah promo masih aktif
    public function getIsPromoActiveAttribute()
    {
        if (!$this->is_promo) return false;
        
        $now = Carbon::now();
        
        // Jika tidak ada tanggal mulai atau tanggal berakhir, cek hanya is_promo
        if (!$this->promo_start && !$this->promo_end) {
            return $this->is_promo;
        }
        
        // Jika ada tanggal mulai tapi tidak ada tanggal berakhir
        if ($this->promo_start && !$this->promo_end) {
            return $this->is_promo && Carbon::parse($this->promo_start)->lte($now);
        }
        
        // Jika tidak ada tanggal mulai tapi ada tanggal berakhir
        if (!$this->promo_start && $this->promo_end) {
            return $this->is_promo && Carbon::parse($this->promo_end)->gte($now);
        }
        
        // Jika ada tanggal mulai dan tanggal berakhir
        return $this->is_promo && 
               Carbon::parse($this->promo_start)->lte($now) && 
               Carbon::parse($this->promo_end)->gte($now);
    }
    
    // Accessor untuk mendapatkan persentase diskon
    public function getDiscountPercentageAttribute()
    {
        if (!$this->is_promo || !$this->promo_price || $this->price <= 0) return 0;
        
        $discount = (($this->price - $this->promo_price) / $this->price) * 100;
        return round($discount);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_product', 'product_id', 'order_id')
                ->withPivot('quantity');
    }

    public function getTotalSalesAttribute()
    {
        return $this->orders()->sum('quantity');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    // Accessor untuk mendapatkan nama promo yang ditampilkan
    public function getPromoDisplayNameAttribute()
    {
        if (!$this->is_promo_active) return null;
        
        return $this->promo_name ?: 'Promo Spesial';
    }

    // Accessor untuk mendapatkan semua produk dengan kategori dan tipe promo yang sama
    public function getRelatedPromotionsAttribute()
    {
        if (!$this->is_promo_active || !$this->promo_type) return collect([]);
        
        return self::where('category', $this->category)
            ->where('promo_type', $this->promo_type)
            ->where('is_promo', true)
            ->where('id', '!=', $this->id)
            ->where('status', 'active')
            ->orderBy('promo_order')
            ->take(5)
            ->get();
    }
}
