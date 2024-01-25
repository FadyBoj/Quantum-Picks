<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $table = "orders";
    protected $primaryKey = "id";

    protected $fillable =[
        "user_id",
        "address",
        "name",
        "total_price"
    ];

    protected $attributes = [
        'status' => "pending",
    ];

    public function items() : HasMany
    {
        return $this->hasMany(Item::class);
    }
}
