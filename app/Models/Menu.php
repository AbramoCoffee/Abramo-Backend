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
        'category_id',
        'description',
        'price',
        'image',
        'qty',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
