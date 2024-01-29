<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'description',
        'price',
        'quantity',
        'category',
        'image'
    ];

    protected $attributes = [
        "rating" => 0
    ];

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }
}
