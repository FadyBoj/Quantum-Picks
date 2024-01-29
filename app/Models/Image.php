<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $table = "product_images";
    protected $primaryKey = "id";
    public $timestamps = false;

    protected $fillable = [
        "image",
        "product_id",
        "public_id"
    ];
}
