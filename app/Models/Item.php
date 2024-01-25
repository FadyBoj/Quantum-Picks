<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'order_items';
    protected $primaryKey = 'primaryID';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'order_id',
        'quantity',
    ];
}
