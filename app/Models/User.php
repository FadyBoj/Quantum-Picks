<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

   protected $table = 'users';
   protected $primaryKey = 'id';

   protected $fillable = [
    "email",
    "firstname",
    "lastname",
    "password",
    "verification_code",
    "vCode_date",
    "complete",
    "google",
    "regular"
   ];
   
   protected $attributes = [
    "google" => false,
    "regular" => false
   ];

   public function cart_items(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
 
}
