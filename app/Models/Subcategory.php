<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'description',
        'category_id',
        'priority',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
