<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'price',
        'image',
        'stock',
        'status',
        'is_favorite',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
