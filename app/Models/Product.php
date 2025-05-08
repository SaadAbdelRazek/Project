<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'image', 'description', 'price',
        'width', 'height', 'length', 'num_in_stock',
        'status', 'priority', 'category_id', 'sub_category_id', 'brand_id', 'acceptance_status','is_in_super_deals','is_in_mega_deals','sale'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'sub_category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_product')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }

    public function photos()
    {
        return $this->hasMany(ProductPhoto::class);
    }



}
