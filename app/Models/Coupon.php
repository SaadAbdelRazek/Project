<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'type', 'discount_value', 'usage_limit',
        'start_date', 'end_date', 'status',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('usage_count')->withTimestamps();
    }
}
